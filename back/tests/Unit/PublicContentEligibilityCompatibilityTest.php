<?php

namespace Tests\Unit;

use App\Support\PublicContentEligibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PublicContentEligibilityCompatibilityTest extends TestCase
{
    public function test_legacy_content_eligibility_does_not_require_the_removed_post_model(): void
    {
        $content = new class extends Model
        {
            protected $guarded = [];
        };
        $content->forceFill([
            'title' => 'Legacy article',
            'excerpt' => '',
            'body' => '',
            'translations' => [
                'fields' => [
                    'title' => ['en' => 'Legacy article'],
                    'content' => ['en' => 'Meaningful section text.'],
                ],
            ],
        ]);

        $section = new class extends Model
        {
            protected $guarded = [];
        };
        $section->forceFill([
            'content' => '<p>Meaningful section text.</p>',
            'translations' => [
                'fields' => [
                    'content' => ['en' => '<p>Meaningful section text.</p>'],
                ],
            ],
        ]);
        $content->setRelation('sections', new Collection([$section]));

        $this->assertTrue(PublicContentEligibility::post($content, 'en'));
    }
}
