<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->unpublishDemoServices();
        $this->unpublishDemoProject();
    }

    public function down(): void
    {
        // Do not publish content implicitly during a rollback.
    }

    private function unpublishDemoServices(): void
    {
        if (! Schema::hasTable('services') || ! Schema::hasColumn('services', 'is_published')) {
            return;
        }

        $fingerprints = [
            'cctv' => [
                'title' => 'CCTV installation and monitoring',
                'description' => 'Professional camera systems for offices, retail, warehouses, and residential buildings.',
            ],
            'networking' => [
                'title' => 'Network Infrastructure',
                'description' => 'Structured cabling, routers, switches, Wi-Fi coverage, and secure business networks.',
            ],
            'access-control' => [
                'title' => 'Access control and attendance systems',
                'description' => 'Card, PIN, biometric, and face-recognition access control for secure workplaces.',
            ],
            'server-infrastructure' => [
                'title' => 'Server infrastructure and virtualization',
                'description' => 'Server deployment, virtualization, storage, backup, and infrastructure monitoring.',
            ],
            'it-support' => [
                'title' => 'Managed IT support for business',
                'description' => 'Remote and on-site IT support, monitoring, asset management, and practical SLA plans.',
            ],
        ];

        foreach ($fingerprints as $slug => $fingerprint) {
            $query = DB::table('services')
                ->where('slug', $slug)
                ->where('title', $fingerprint['title'])
                ->where('description', $fingerprint['description'])
                ->where('is_published', true);

            if (Schema::hasColumn('services', 'hero_image')) {
                $query->whereNull('hero_image');
            }

            $this->onlyUntouchedRecords($query, 'services');
            $this->withoutMedia($query, 'services', 'App\\Models\\Service');

            $query->update([
                'is_published' => false,
            ]);
        }
    }

    private function unpublishDemoProject(): void
    {
        if (! Schema::hasTable('projects') || ! Schema::hasColumn('projects', 'is_published')) {
            return;
        }

        $query = DB::table('projects')
            ->where('slug', 'office-network-upgrade')
            ->where('title', 'Office Network Upgrade')
            ->where('description', 'A complete network refresh with structured cabling, managed switching, and Wi-Fi coverage.')
            ->where('is_published', true);

        if (Schema::hasColumn('projects', 'image')) {
            $query->whereNull('image');
        }

        $this->onlyUntouchedRecords($query, 'projects');
        $this->withoutMedia($query, 'projects', 'App\\Models\\Project');

        $updates = [
            'is_published' => false,
        ];

        if (Schema::hasColumn('projects', 'is_featured')) {
            $updates['is_featured'] = false;
        }

        $query->update($updates);
    }

    private function onlyUntouchedRecords(Builder $query, string $table): void
    {
        if (Schema::hasColumn($table, 'created_at') && Schema::hasColumn($table, 'updated_at')) {
            $query->whereColumn("{$table}.created_at", "{$table}.updated_at");
        }
    }

    private function withoutMedia(Builder $query, string $table, string $modelType): void
    {
        if (! Schema::hasTable('media')) {
            return;
        }

        $query->whereNotExists(function (Builder $media) use ($table, $modelType): void {
            $media
                ->selectRaw('1')
                ->from('media')
                ->whereColumn('media.model_id', "{$table}.id")
                ->where('media.model_type', $modelType);
        });
    }
};
