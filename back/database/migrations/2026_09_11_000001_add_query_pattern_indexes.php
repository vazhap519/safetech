<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table): void {
            $table->index(['is_published', 'sort_order'], 'services_public_order_index');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->index(
                ['is_published', 'published_at', 'sort_order'],
                'projects_public_schedule_order_index',
            );
            $table->index(
                ['is_published', 'is_featured', 'sort_order'],
                'projects_public_featured_order_index',
            );
        });

        Schema::table('faqs', function (Blueprint $table): void {
            $table->index(
                ['service_id', 'is_active', 'sort_order'],
                'faqs_service_active_order_index',
            );
        });

        foreach (['team_members', 'partners', 'testimonials'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->index(
                    ['is_active', 'sort_order'],
                    "{$tableName}_active_order_index",
                );
            });
        }

        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->index(['status', 'created_at'], 'contact_leads_status_created_index');
        });

        Schema::table('analytics_events', function (Blueprint $table): void {
            $table->index(
                ['page_path', 'event_type', 'visitor_hash'],
                'analytics_page_type_visitor_index',
            );
            $table->index(
                ['service_id', 'event_type', 'visitor_hash'],
                'analytics_service_type_visitor_index',
            );
        });

        Schema::table('local_service_landings', function (Blueprint $table): void {
            $table->index(
                ['is_published', 'published_at', 'sort_order'],
                'local_landings_public_schedule_order_index',
            );
            $table->index(
                ['service_id', 'is_published', 'sort_order'],
                'local_landings_service_public_order_index',
            );
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            $table->index(['is_public', 'key'], 'site_settings_public_key_index');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            $table->dropIndex('site_settings_public_key_index');
        });

        Schema::table('local_service_landings', function (Blueprint $table): void {
            $table->dropIndex('local_landings_public_schedule_order_index');
            $table->dropIndex('local_landings_service_public_order_index');
        });

        Schema::table('analytics_events', function (Blueprint $table): void {
            $table->dropIndex('analytics_page_type_visitor_index');
            $table->dropIndex('analytics_service_type_visitor_index');
        });

        Schema::table('contact_leads', function (Blueprint $table): void {
            $table->dropIndex('contact_leads_status_created_index');
        });

        foreach (['team_members', 'partners', 'testimonials'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                $table->dropIndex("{$tableName}_active_order_index");
            });
        }

        Schema::table('faqs', function (Blueprint $table): void {
            $table->dropIndex('faqs_service_active_order_index');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->dropIndex('projects_public_schedule_order_index');
            $table->dropIndex('projects_public_featured_order_index');
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropIndex('services_public_order_index');
        });
    }
};
