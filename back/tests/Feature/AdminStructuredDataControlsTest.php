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

    public function test_shared_schema_controls_use_clear_override_and_generated_preview_labels(): void
    {
        $preview = file_get_contents(base_path('app/Filament/Support/GeneratedSchemaPreview.php'));
        $override = file_get_contents(base_path('app/Filament/Support/StructuredDataJsonField.php'));

        $this->assertIsString($preview);
        $this->assertIsString($override);
        $this->assertStringContainsString('ავტომატურად გენერირებული Schema JSON-LD', $preview);
        $this->assertStringContainsString('Custom Schema JSON-LD override', $override);
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
