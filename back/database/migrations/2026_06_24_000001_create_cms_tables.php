<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createOrUpgradeServicesTable();

        $this->createOrUpgradeProjectsTable();

        if (! Schema::hasTable('team_members')) {
            Schema::create('team_members', function (Blueprint $table): void {
                $table->id();
                $table->string('first_name', 60);
                $table->string('last_name', 60);
                $table->string('position');
                $table->string('image')->nullable();
                $table->text('bio')->nullable();
                $table->json('socials')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('logo')->nullable();
                $table->string('url')->nullable();
                $table->string('category')->nullable()->index();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table): void {
                $table->id();
                $table->text('quote');
                $table->string('author');
                $table->string('role')->nullable();
                $table->string('company')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('faqs')) {
            Schema::create('faqs', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
                $table->string('context')->default('general')->index();
                $table->text('question');
                $table->text('answer');
                $table->boolean('is_active')->default(true)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('site_settings')) {
            Schema::create('site_settings', function (Blueprint $table): void {
                $table->id();
                $table->string('key')->unique();
                $table->string('group')->default('general')->index();
                $table->json('value')->nullable();
                $table->boolean('is_public')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->boolean('is_admin')->default(false)->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
        }
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('projects');
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table): void {
                foreach ([
                    'name',
                    'eyebrow',
                    'icon',
                    'seo_description',
                    'hero_image',
                    'keywords',
                    'highlights',
                    'overview',
                    'benefits',
                    'solutions',
                    'industries',
                    'process',
                    'brands',
                    'warranty',
                    'sla',
                    'is_published',
                    'sort_order',
                ] as $column) {
                    if (Schema::hasColumn('services', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }

    private function createOrUpgradeServicesTable(): void
    {
        if (! Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table): void {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('eyebrow')->nullable();
                $table->string('icon')->default('settings');
                $table->string('title');
                $table->text('description');
                $table->text('seo_description');
                $table->string('hero_image')->nullable();
                $table->json('keywords')->nullable();
                $table->json('highlights')->nullable();
                $table->json('overview')->nullable();
                $table->json('benefits')->nullable();
                $table->json('solutions')->nullable();
                $table->json('industries')->nullable();
                $table->json('process')->nullable();
                $table->json('brands')->nullable();
                $table->text('warranty')->nullable();
                $table->text('sla')->nullable();
                $table->boolean('is_published')->default(false)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamps();
            });

            return;
        }

        Schema::table('services', function (Blueprint $table): void {
            if (! Schema::hasColumn('services', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('services', 'eyebrow')) {
                $table->string('eyebrow')->nullable();
            }
            if (! Schema::hasColumn('services', 'icon')) {
                $table->string('icon')->default('settings');
            }
            if (! Schema::hasColumn('services', 'seo_description')) {
                $table->text('seo_description')->nullable();
            }
            if (! Schema::hasColumn('services', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('services', 'hero_image')) {
                $table->string('hero_image')->nullable();
            }
            foreach ([
                'keywords',
                'highlights',
                'overview',
                'benefits',
                'solutions',
                'industries',
                'process',
                'brands',
            ] as $column) {
                if (! Schema::hasColumn('services', $column)) {
                    $table->json($column)->nullable();
                }
            }
            if (! Schema::hasColumn('services', 'warranty')) {
                $table->text('warranty')->nullable();
            }
            if (! Schema::hasColumn('services', 'sla')) {
                $table->text('sla')->nullable();
            }
            if (! Schema::hasColumn('services', 'is_published')) {
                $table->boolean('is_published')->default(false)->index();
            }
            if (! Schema::hasColumn('services', 'sort_order')) {
                $table->unsignedInteger('sort_order')->default(0)->index();
            }
        });

        DB::table('services')
            ->whereNull('name')
            ->update(['name' => DB::raw('title')]);

        if (Schema::hasColumn('services', 'description')) {
            if (Schema::hasColumn('services', 'short_description')) {
                DB::table('services')
                    ->whereNull('description')
                    ->update(['description' => DB::raw('short_description')]);
            } elseif (Schema::hasColumn('services', 'long_description')) {
                DB::table('services')
                    ->whereNull('description')
                    ->update(['description' => DB::raw('long_description')]);
            }
        }

        if (Schema::hasColumn('services', 'seo_description')) {
            foreach (['description', 'short_description', 'long_description', 'title'] as $fallbackColumn) {
                if (! Schema::hasColumn('services', $fallbackColumn)) {
                    continue;
                }

                DB::table('services')
                    ->whereNull('seo_description')
                    ->update(['seo_description' => DB::raw($fallbackColumn)]);

                break;
            }
        }
    }

    private function createOrUpgradeProjectsTable(): void
    {
        if (! Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table): void {
                $table->id();
                $table->string('slug')->unique();
                $table->string('name');
                $table->string('title');
                $table->text('description');
                $table->text('seo_description');
                $table->string('image')->nullable();
                $table->string('image_alt')->nullable();
                $table->string('category')->default('offices')->index();
                $table->string('technology')->nullable();
                $table->string('icon')->default('business');
                $table->string('accent', 20)->default('primary');
                $table->json('meta')->nullable();
                $table->json('scope')->nullable();
                $table->json('specs')->nullable();
                $table->json('challenges')->nullable();
                $table->json('solutions')->nullable();
                $table->json('process')->nullable();
                $table->json('gallery')->nullable();
                $table->json('results')->nullable();
                $table->json('testimonial')->nullable();
                $table->json('related')->nullable();
                $table->boolean('is_featured')->default(false)->index();
                $table->boolean('is_published')->default(false)->index();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
            });

            return;
        }

        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'name')) {
                $table->string('name')->nullable();
            }
            if (! Schema::hasColumn('projects', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('projects', 'seo_description')) {
                $table->text('seo_description')->nullable();
            }
            if (! Schema::hasColumn('projects', 'image')) {
                $table->string('image')->nullable();
            }
            if (! Schema::hasColumn('projects', 'image_alt')) {
                $table->string('image_alt')->nullable();
            }
            if (! Schema::hasColumn('projects', 'category')) {
                $table->string('category')->nullable()->index();
            }
            if (! Schema::hasColumn('projects', 'technology')) {
                $table->string('technology')->nullable();
            }
            if (! Schema::hasColumn('projects', 'icon')) {
                $table->string('icon')->default('business');
            }
            if (! Schema::hasColumn('projects', 'accent')) {
                $table->string('accent', 20)->default('primary');
            }
            foreach ([
                'meta',
                'scope',
                'specs',
                'challenges',
                'solutions',
                'process',
                'gallery',
                'results',
                'testimonial',
                'related',
            ] as $column) {
                if (! Schema::hasColumn('projects', $column)) {
                    $table->json($column)->nullable();
                }
            }
            if (! Schema::hasColumn('projects', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->index();
            }
        });

        DB::table('projects')
            ->whereNull('name')
            ->update(['name' => DB::raw('title')]);

        if (Schema::hasColumn('projects', 'description')) {
            foreach (['excerpt', 'content', 'title'] as $fallbackColumn) {
                if (! Schema::hasColumn('projects', $fallbackColumn)) {
                    continue;
                }

                DB::table('projects')
                    ->whereNull('description')
                    ->update(['description' => DB::raw($fallbackColumn)]);

                break;
            }
        }

        if (Schema::hasColumn('projects', 'seo_description')) {
            foreach (['description', 'excerpt', 'content', 'title'] as $fallbackColumn) {
                if (! Schema::hasColumn('projects', $fallbackColumn)) {
                    continue;
                }

                DB::table('projects')
                    ->whereNull('seo_description')
                    ->update(['seo_description' => DB::raw($fallbackColumn)]);

                break;
            }
        }
    }
};
