<?php

namespace App\Filament\Support;

use App\Support\CmsMedia;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class CmsMediaUpload
{
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
        $disk = config('livewire.temporary_file_upload.disk')
            ?: config('filesystems.default', 'local');
        $directory = config('livewire.temporary_file_upload.directory')
            ?: 'livewire-tmp';

        try {
            Storage::disk((string) $disk)->makeDirectory((string) $directory);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    private function __construct() {}
}
