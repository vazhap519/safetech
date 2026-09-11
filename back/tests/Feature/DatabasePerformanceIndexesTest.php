<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DatabasePerformanceIndexesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_provides_composite_indexes_for_public_and_reporting_queries(): void
    {
        $expected = [
            'services' => ['services_public_order_index'],
            'projects' => [
                'projects_public_schedule_order_index',
                'projects_public_featured_order_index',
            ],
            'faqs' => ['faqs_service_active_order_index'],
            'team_members' => ['team_members_active_order_index'],
            'partners' => ['partners_active_order_index'],
            'testimonials' => ['testimonials_active_order_index'],
            'contact_leads' => ['contact_leads_status_created_index'],
            'analytics_events' => [
                'analytics_page_type_visitor_index',
                'analytics_service_type_visitor_index',
            ],
            'local_service_landings' => [
                'local_landings_public_schedule_order_index',
                'local_landings_service_public_order_index',
            ],
            'site_settings' => ['site_settings_public_key_index'],
        ];

        foreach ($expected as $table => $requiredIndexes) {
            $availableIndexes = collect(Schema::getIndexes($table))->pluck('name');

            foreach ($requiredIndexes as $requiredIndex) {
                $this->assertTrue(
                    $availableIndexes->contains($requiredIndex),
                    "Missing expected index [{$requiredIndex}] on [{$table}].",
                );
            }
        }
    }
}
