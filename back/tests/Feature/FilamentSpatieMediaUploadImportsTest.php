<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FilamentSpatieMediaUploadImportsTest extends TestCase
{
    #[Test]
    public function every_filament_file_using_spatie_media_upload_imports_the_component(): void
    {
        $root = app_path('Filament');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));
        $missing = [];

        foreach ($files as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($file->getPathname());

            if (! str_contains($source, 'SpatieMediaLibraryFileUpload::')) {
                continue;
            }

            if (! preg_match('/^use\\s+Filament\\\\Forms\\\\Components\\\\SpatieMediaLibraryFileUpload\\s*;/m', $source)) {
                $missing[] = $file->getPathname();
            }
        }

        $this->assertSame([], $missing, 'Missing SpatieMediaLibraryFileUpload imports: '.implode(', ', $missing));
    }
}
