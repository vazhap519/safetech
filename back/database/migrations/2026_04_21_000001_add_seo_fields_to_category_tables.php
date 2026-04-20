<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'categories',
        'category_for_services',
        'project_categories',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'seo_title')) {
                    $table->string('seo_title')->nullable()->after('slug');
                }

                if (!Schema::hasColumn($tableName, 'seo_description')) {
                    $table->text('seo_description')->nullable()->after('seo_title');
                }

                if (!Schema::hasColumn($tableName, 'seo_keywords')) {
                    $table->json('seo_keywords')->nullable()->after('seo_description');
                }

                if (!Schema::hasColumn($tableName, 'intro_text')) {
                    $table->longText('intro_text')->nullable()->after('seo_keywords');
                }

                if (!Schema::hasColumn($tableName, 'faq')) {
                    $table->json('faq')->nullable()->after('intro_text');
                }

                if (!Schema::hasColumn($tableName, 'schema')) {
                    $table->json('schema')->nullable()->after('faq');
                }

                if (!Schema::hasColumn($tableName, 'noindex')) {
                    $table->boolean('noindex')->default(false)->after('schema');
                }
            });
        }

        if (Schema::hasTable('project_categories') && !Schema::hasColumn('project_categories', 'color')) {
            Schema::table('project_categories', function (Blueprint $table) {
                $table->string('color')->nullable()->after('slug');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = collect([
                    'seo_title',
                    'seo_description',
                    'seo_keywords',
                    'intro_text',
                    'faq',
                    'schema',
                    'noindex',
                ])->filter(fn ($column) => Schema::hasColumn($tableName, $column))->values()->all();

                if ($columns) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('project_categories') && Schema::hasColumn('project_categories', 'color')) {
            Schema::table('project_categories', function (Blueprint $table) {
                $table->dropColumn('color');
            });
        }
    }
};
