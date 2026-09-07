<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminStructuredDataControlsTest extends TestCase
{
    #[DataProvider('editorSourceProvider')]
    public function test_public_content_editors_expose_generated_schema_controls(
        string $path,
        string $previewCall,
        string $overridePath,
    ): void {
        $source = file_get_contents(base_path($path));

        $this->assertIsString($source);
        $this->assertStringContainsString($previewCall, $source);
        $this->assertStringContainsString($overridePath, $source);
    }

    public static function editorSourceProvider(): array
    {
        return [
            'service' => [
                'app/Filament/Resources/ServiceResource.php',
                'GeneratedSchemaPreview::service()',
                "'seo.schema'",
            ],
            'project' => [
                'app/Filament/Resources/ProjectResource.php',
                'GeneratedSchemaPreview::project()',
                "'seo.schema'",
            ],
            'local service landing' => [
                'app/Filament/Resources/LocalServiceLandingResource.php',
                'GeneratedSchemaPreview::localService()',
                "'schema'",
            ],
            'generic page' => [
                'app/Filament/Resources/PageResource.php',
                'GeneratedSchemaPreview::page()',
                "'schema'",
            ],
            'shared category SEO' => [
                'app/Filament/Support/CategorySeoFields.php',
                'GeneratedSchemaPreview::category($kind)',
                "'schema'",
            ],
        ];
    }
}
