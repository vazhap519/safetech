<?php

namespace App\Console\Commands;

use App\Filament\Support\CmsMediaUpload;
use App\Support\CmsMedia;
use App\Support\DeploymentInfo;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
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

        $signedUrl = $this->publicLivewireUploadUrl($baseUrl);

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

    private function publicLivewireUploadUrl(string $baseUrl): string
    {
        $localCommit = DeploymentInfo::commit();

        if ($localCommit === null) {
            throw new RuntimeException('Unable to determine the local Git commit for the HTTP upload smoke test.');
        }

        $appKey = (string) config('app.key');

        if ($appKey === '') {
            throw new RuntimeException('APP_KEY is unavailable for the HTTP upload probe.');
        }

        $nonce = Str::uuid()->toString();
        $probeSignature = hash_hmac('sha256', $nonce, $appKey);

        $response = Http::acceptJson()
            ->withHeaders([
                'X-SafeTech-Upload-Probe-Nonce' => $nonce,
                'X-SafeTech-Upload-Probe-Signature' => $probeSignature,
            ])
            ->timeout(20)
            ->connectTimeout(10)
            ->get($baseUrl.'/api/health', [
                'deployment_check' => Str::uuid()->toString(),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException(
                "The public API health check failed with HTTP {$response->status()}.",
            );
        }

        $publicCommit = $response->json('commit');

        if (! is_string($publicCommit) || $publicCommit === '') {
            throw new RuntimeException(
                'The public API is not serving the deployed backend. Check that the api.safetech.ge Nginx root points to '
                .public_path().'.',
            );
        }

        if (! hash_equals($localCommit, strtolower($publicCommit))) {
            throw new RuntimeException(
                "The public API is serving commit {$publicCommit}, but the deployed checkout is {$localCommit}. "
                .'Check the api.safetech.ge Nginx document root and PHP-FPM opcache.',
            );
        }

        $requestRoot = rtrim((string) $response->json('request_root'), '/');

        if ($requestRoot === '' || ! hash_equals(strtolower($baseUrl), strtolower($requestRoot))) {
            throw new RuntimeException(
                "The public backend sees request root '{$requestRoot}', expected '{$baseUrl}'. "
                .'Check trusted proxies and X-Forwarded-Host/X-Forwarded-Proto.',
            );
        }

        $signedUrl = (string) $response->json('livewire_upload_url');

        if ($signedUrl === '') {
            throw new RuntimeException(
                'The public backend did not return a Livewire signed upload URL. '
                .'Check APP_KEY, route registration, and production URL generation.',
            );
        }

        $signedScheme = parse_url($signedUrl, PHP_URL_SCHEME);
        $signedHost = parse_url($signedUrl, PHP_URL_HOST);
        $signedPort = parse_url($signedUrl, PHP_URL_PORT);
        $signedPath = parse_url($signedUrl, PHP_URL_PATH);
        $baseScheme = parse_url($baseUrl, PHP_URL_SCHEME);
        $baseHost = parse_url($baseUrl, PHP_URL_HOST);
        $basePort = parse_url($baseUrl, PHP_URL_PORT);

        if (
            ! is_string($signedScheme)
            || ! is_string($signedHost)
            || strtolower($signedScheme) !== strtolower((string) $baseScheme)
            || strtolower($signedHost) !== strtolower((string) $baseHost)
            || $signedPort !== $basePort
        ) {
            throw new RuntimeException(
                "Livewire generated the upload URL '{$signedUrl}', expected origin '{$baseUrl}'. "
                .'Check APP_URL and reverse-proxy HTTPS headers.',
            );
        }

        if (! is_string($signedPath) || ! str_contains($signedPath, '/livewire/upload-file')) {
            throw new RuntimeException(
                "Livewire generated an unexpected upload path: '{$signedUrl}'.",
            );
        }

        return $signedUrl;
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
