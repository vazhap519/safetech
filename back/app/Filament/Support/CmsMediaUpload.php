<?php

namespace App\Filament\Support;

use App\Support\CmsMedia;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final class CmsMediaUpload
{
    private const NGINX_CONFIG_TARGET = '/etc/nginx/conf.d/safetech-upload-limits.conf';

    public static function registerDefaults(): void
    {
        SpatieMediaLibraryFileUpload::configureUsing(
            fn (SpatieMediaLibraryFileUpload $upload): SpatieMediaLibraryFileUpload => $upload
                ->acceptedFileTypes(CmsMedia::IMAGE_MIME_TYPES)
                ->maxSize(CmsMedia::MAX_FILE_SIZE_KB)
                ->imagePreviewHeight('180')
                ->orientImagesFromExif()
                ->openable()
                ->downloadable()
                ->uploadingMessage('ფოტო იტვირთება, გთხოვთ დაელოდოთ...'),
        );
    }

    public static function ensureTemporaryDirectory(): void
    {
        $disk = self::temporaryDisk();
        $directory = self::temporaryDirectory();

        try {
            Storage::disk($disk)->makeDirectory($directory);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    public static function installProductionNginxLimits(): void
    {
        if (! app()->runningInConsole() || ! app()->environment('production')) {
            return;
        }

        if (function_exists('posix_geteuid') && posix_geteuid() !== 0) {
            return;
        }

        $source = self::nginxConfigSourcePath();
        $target = self::NGINX_CONFIG_TARGET;
        $targetDirectory = dirname($target);

        if (! is_file('/etc/nginx/nginx.conf') || ! is_dir($targetDirectory)) {
            return;
        }

        $content = file_get_contents($source);

        if ($content === false) {
            throw new RuntimeException("Unable to read the Nginx upload configuration: {$source}");
        }

        if (is_file($target) && hash_file('sha256', $target) === hash('sha256', $content)) {
            return;
        }

        $temporaryPath = tempnam($targetDirectory, '.safetech-upload-');

        if ($temporaryPath === false) {
            throw new RuntimeException("Unable to create a temporary Nginx configuration in {$targetDirectory}");
        }

        try {
            if (file_put_contents($temporaryPath, $content, LOCK_EX) === false) {
                throw new RuntimeException("Unable to write the Nginx upload configuration: {$temporaryPath}");
            }

            chmod($temporaryPath, 0644);

            if (! rename($temporaryPath, $target)) {
                throw new RuntimeException("Unable to install the Nginx upload configuration: {$target}");
            }
        } finally {
            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }
        }
    }

    public static function temporaryDisk(): string
    {
        return (string) (config('livewire.temporary_file_upload.disk')
            ?: config('filesystems.default', 'local'));
    }

    public static function temporaryDirectory(): string
    {
        return (string) (config('livewire.temporary_file_upload.directory')
            ?: 'livewire-tmp');
    }

    public static function nginxConfigSourcePath(): string
    {
        return base_path('deploy/nginx/safetech-upload-limits.conf');
    }

    public static function nginxConfigTargetPath(): string
    {
        return self::NGINX_CONFIG_TARGET;
    }

    private function __construct() {}
}
