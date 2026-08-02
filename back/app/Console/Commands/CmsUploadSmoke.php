<?php

namespace App\Console\Commands;

use App\Filament\Support\CmsMediaUpload;
use App\Support\CmsMedia;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CmsUploadSmoke extends Command
{
    protected $signature = 'cms:upload-smoke {--check-nginx-runtime : Require the installed Nginx configuration to match the repository copy}';

    protected $description = 'Run end-to-end configuration and storage checks for Filament/Livewire image uploads';

    public function handle(): int
    {
        try {
            $this->assertLivewireUploadRouteExists();
            $this->assertUploadRulesAcceptARealPng();
            $this->assertProxyAndPhpLimits();
            $this->assertStorageRoundTrip();

            if ($this->option('check-nginx-runtime')) {
                $this->assertInstalledNginxConfiguration();
            }
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('CMS upload smoke test passed.');

        return self::SUCCESS;
    }

    private function assertLivewireUploadRouteExists(): void
    {
        $routeExists = collect(Route::getRoutes())->contains(
            fn ($route): bool => in_array('POST', $route->methods(), true)
                && str_contains($route->uri(), 'livewire/upload-file'),
        );

        if (! $routeExists) {
            throw new RuntimeException('The Livewire temporary upload route is not registered.');
        }
    }

    private function assertUploadRulesAcceptARealPng(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'safetech-upload-smoke-');

        if ($path === false) {
            throw new RuntimeException('Unable to create the upload validation probe.');
        }

        try {
            file_put_contents($path, $this->pngPayload());

            $upload = new UploadedFile(
                $path,
                'safetech-upload-smoke.png',
                'image/png',
                UPLOAD_ERR_OK,
                true,
            );
            $rules = config('livewire.temporary_file_upload.rules', []);
            $validator = Validator::make(['upload' => $upload], ['upload' => $rules]);

            if ($validator->fails()) {
                throw new RuntimeException(
                    'Livewire rejected a valid 2 MB PNG: '.$validator->errors()->first('upload'),
                );
            }
        } finally {
            @unlink($path);
        }
    }

    private function assertProxyAndPhpLimits(): void
    {
        $minimumBytes = CmsMedia::MAX_FILE_SIZE_KB * 1024;
        $userIni = file_get_contents(public_path('.user.ini'));
        $nginx = file_get_contents(CmsMediaUpload::nginxConfigSourcePath());

        if ($userIni === false || $nginx === false) {
            throw new RuntimeException('Unable to read the PHP or Nginx upload limit configuration.');
        }

        $phpUploadBytes = $this->directiveBytes($userIni, 'upload_max_filesize');
        $phpPostBytes = $this->directiveBytes($userIni, 'post_max_size');
        $nginxBytes = $this->directiveBytes($nginx, 'client_max_body_size');

        if ($phpUploadBytes < $minimumBytes) {
            throw new RuntimeException('PHP upload_max_filesize is lower than the CMS upload limit.');
        }

        if ($phpPostBytes <= $minimumBytes) {
            throw new RuntimeException('PHP post_max_size must be larger than the CMS upload limit.');
        }

        if ($nginxBytes <= $minimumBytes) {
            throw new RuntimeException('Nginx client_max_body_size must be larger than the CMS upload limit.');
        }
    }

    private function assertStorageRoundTrip(): void
    {
        $identifier = (string) Str::uuid();
        $temporaryPath = CmsMediaUpload::temporaryDirectory()."/{$identifier}.png";
        $publicPath = "cms-upload-smoke/{$identifier}.png";
        $temporaryDisk = Storage::disk(CmsMediaUpload::temporaryDisk());
        $publicDisk = Storage::disk('public');
        $payload = $this->pngPayload();

        try {
            if (! $temporaryDisk->put($temporaryPath, $payload)) {
                throw new RuntimeException('The Livewire temporary upload disk is not writable.');
            }

            if ($temporaryDisk->size($temporaryPath) !== strlen($payload)) {
                throw new RuntimeException('The temporary upload was truncated.');
            }

            if (! $publicDisk->put($publicPath, $temporaryDisk->get($temporaryPath))) {
                throw new RuntimeException('The public media disk is not writable.');
            }

            if ($publicDisk->size($publicPath) !== strlen($payload)) {
                throw new RuntimeException('The permanent media copy was truncated.');
            }
        } finally {
            $temporaryDisk->delete($temporaryPath);
            $publicDisk->delete($publicPath);
        }
    }

    private function assertInstalledNginxConfiguration(): void
    {
        $source = CmsMediaUpload::nginxConfigSourcePath();
        $target = CmsMediaUpload::nginxConfigTargetPath();

        if (! is_file($target)) {
            throw new RuntimeException("The production Nginx upload configuration is missing: {$target}");
        }

        if (hash_file('sha256', $source) !== hash_file('sha256', $target)) {
            throw new RuntimeException('The installed Nginx upload configuration is stale.');
        }
    }

    private function directiveBytes(string $configuration, string $directive): int
    {
        if (! preg_match('/^'.preg_quote($directive, '/').'\s*=??\s*([0-9]+)\s*([kmgt]?)\s*;?$/mi', $configuration, $matches)) {
            throw new RuntimeException("Missing upload directive: {$directive}");
        }

        $bytes = (int) $matches[1];
        $unit = strtolower($matches[2] ?? '');
        $powers = ['' => 0, 'k' => 1, 'm' => 2, 'g' => 3, 't' => 4];

        return $bytes * (1024 ** $powers[$unit]);
    }

    private function pngPayload(): string
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAusB9Y9Z1pAAAAAASUVORK5CYII=',
            true,
        );

        if ($png === false) {
            throw new RuntimeException('Unable to build the PNG smoke-test payload.');
        }

        return $png.str_repeat("\0", (2 * 1024 * 1024) - strlen($png));
    }
}
