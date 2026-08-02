<?php

namespace App\Console\Commands;

use App\Filament\Support\CmsMediaUpload;
use App\Support\CmsMedia;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CmsUploadSmoke extends Command
{
    protected $signature = 'cms:upload-smoke
        {--check-nginx-runtime : Require the installed Nginx configuration to match the repository copy}
        {--http-base-url= : Upload a real 2 MB image through the public Livewire HTTP endpoint}';

    protected $description = 'Run end-to-end configuration, storage, and optional public HTTP checks for Filament/Livewire uploads';

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

            $httpBaseUrl = trim((string) $this->option('http-base-url'));

            if ($httpBaseUrl !== '') {
                $this->assertPublicHttpUpload($httpBaseUrl);
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
        $route = Route::getRoutes()->getByName('livewire.upload-file');

        if ($route === null || ! in_array('POST', $route->methods(), true)) {
            throw new RuntimeException('The named Livewire temporary upload route is not registered.');
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

    private function assertPublicHttpUpload(string $baseUrl): void
    {
        $baseUrl = rtrim($baseUrl, '/');
        $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
        $host = parse_url($baseUrl, PHP_URL_HOST);

        if (! is_string($scheme) || ! in_array($scheme, ['http', 'https'], true) || ! is_string($host)) {
            throw new RuntimeException('The HTTP upload smoke-test base URL is invalid.');
        }

        URL::forceRootUrl($baseUrl);
        URL::forceScheme($scheme);

        $signedUrl = URL::temporarySignedRoute(
            'livewire.upload-file',
            now()->addMinutes(5),
        );

        $response = Http::acceptJson()
            ->timeout(60)
            ->connectTimeout(10)
            ->attach(
                'files[]',
                $this->pngPayload(),
                'safetech-upload-smoke.png',
                ['Content-Type' => 'image/png'],
            )
            ->post($signedUrl);

        $this->assertSuccessfulUploadResponse($response, $signedUrl);
    }

    private function assertSuccessfulUploadResponse(Response $response, string $signedUrl): void
    {
        if (! $response->successful()) {
            $body = Str::limit(trim($response->body()), 1000, '…');
            $path = parse_url($signedUrl, PHP_URL_PATH) ?: '/livewire/upload-file';

            throw new RuntimeException(
                "Public Livewire upload failed with HTTP {$response->status()} at {$path}. Response: {$body}",
            );
        }

        $paths = $response->json('paths');

        if (! is_array($paths) || $paths === []) {
            throw new RuntimeException(
                'Public Livewire upload returned success without a temporary file path. Response: '
                .Str::limit(trim($response->body()), 1000, '…'),
            );
        }

        $temporaryDisk = Storage::disk(CmsMediaUpload::temporaryDisk());

        foreach ($paths as $path) {
            if (is_string($path) && $path !== '') {
                $temporaryDisk->delete($path);
            }
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
