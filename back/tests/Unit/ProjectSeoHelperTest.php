<?php

namespace Tests\Unit;

use App\Filament\Support\ProjectSeoHelper;
use PHPUnit\Framework\TestCase;

class ProjectSeoHelperTest extends TestCase
{
    public function test_it_builds_suggestions_from_verified_project_facts(): void
    {
        $result = ProjectSeoHelper::suggest(
            'Bakuriani cottage CCTV',
            '6 კამერიანი TVT 4MP Full Color ვიდეოსამეთვალყურეო სისტემა',
            'კოტეჯისთვის დამონტაჟდა სრული ვიდეოსამეთვალყურეო სისტემა.',
            'ბაკურიანი',
            'კოტეჯი',
            [
                ['name' => 'TVT Full Color კამერა', 'model' => 'TD-9444', 'quantity' => '6 ცალი'],
                ['name' => 'TVT PoE NVR', 'model' => '8-port', 'quantity' => '1 ცალი'],
            ],
        );

        $this->assertStringContainsString('ბაკურიანი', $result['title']);
        $this->assertStringContainsString('კოტეჯი', $result['description']);
        $this->assertStringContainsString('TVT Full Color კამერა', $result['description']);
        $this->assertStringContainsString('SafeTech', $result['imageAlt']);
        $this->assertLessThanOrEqual(68, mb_strlen($result['title']));
        $this->assertLessThanOrEqual(170, mb_strlen($result['description']));
        $this->assertLessThanOrEqual(140, mb_strlen($result['imageAlt']));
    }

    public function test_it_does_not_duplicate_city_when_headline_already_contains_it(): void
    {
        $result = ProjectSeoHelper::suggest(
            null,
            'კამერების მონტაჟი ბაკურიანში',
            null,
            'ბაკურიანი',
            'კოტეჯი',
        );

        $this->assertSame(1, mb_substr_count(mb_strtolower($result['title']), 'ბაკურიანი'));
    }
}
