<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $this->connectServices();
        $this->connectProjects();
    }

    public function down(): void
    {
        // Data is intentionally retained: removing these relations would orphan production content.
    }

    private function connectServices(): void
    {
        if (! Schema::hasTable('services') || ! Schema::hasTable('category_for_services')) {
            return;
        }

        $definitions = [
            'security-systems' => [
                'services' => ['cctv', 'access-control'],
                'names' => ['უსაფრთხოების სისტემები', 'Security Systems', 'Системы безопасности'],
            ],
            'network-infrastructure' => [
                'services' => ['networking'],
                'names' => ['ქსელური ინფრასტრუქტურა', 'Network Infrastructure', 'Сетевая инфраструктура'],
            ],
            'server-infrastructure' => [
                'services' => ['server-infrastructure'],
                'names' => ['სერვერული ინფრასტრუქტურა', 'Server Infrastructure', 'Серверная инфраструктура'],
            ],
            'it-support' => [
                'services' => ['it-support'],
                'names' => ['IT მხარდაჭერა', 'IT Support', 'IT-поддержка'],
            ],
        ];

        foreach ($definitions as $slug => $definition) {
            $categoryId = $this->categoryId(
                'category_for_services',
                $slug,
                $definition['names'],
            );

            DB::table('services')
                ->whereIn('slug', $definition['services'])
                ->whereNull('category_for_service_id')
                ->update(['category_for_service_id' => $categoryId]);
        }
    }

    private function connectProjects(): void
    {
        if (! Schema::hasTable('projects') || ! Schema::hasTable('project_categories')) {
            return;
        }

        if (! Schema::hasColumn('projects', 'category')) {
            return;
        }

        DB::table('projects')
            ->whereNull('category_id')
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->each(function ($project): void {
                $legacyCategory = trim((string) $project->category);

                if ($legacyCategory === '') {
                    return;
                }

                $slug = Str::slug($legacyCategory) ?: 'category-'.substr(sha1($legacyCategory), 0, 12);
                $names = $slug === 'offices'
                    ? ['ოფისები', 'Offices', 'Офисы']
                    : [Str::headline($legacyCategory), Str::headline($legacyCategory), Str::headline($legacyCategory)];
                $categoryId = $this->categoryId('project_categories', $slug, $names);

                DB::table('projects')
                    ->whereNull('category_id')
                    ->where('category', $legacyCategory)
                    ->update(['category_id' => $categoryId]);
            });
    }

    /** @param array{0: string, 1: string, 2: string} $names */
    private function categoryId(string $table, string $slug, array $names): int
    {
        $existingId = DB::table($table)->where('slug', $slug)->value('id');

        if ($existingId) {
            return (int) $existingId;
        }

        $record = [
            'name' => $names[0],
            'slug' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn($table, 'translations')) {
            $record['translations'] = json_encode([
                'fields' => [
                    'name' => ['ka' => $names[0], 'en' => $names[1], 'ru' => $names[2]],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        if (Schema::hasColumn($table, 'sort_order')) {
            $record['sort_order'] = 0;
        }

        return (int) DB::table($table)->insertGetId($record);
    }
};
