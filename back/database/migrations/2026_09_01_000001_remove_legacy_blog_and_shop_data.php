<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->removeSeoPages();
        $this->removeTranslationKeys();

        Schema::dropIfExists('post_sections');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_filters');
        Schema::dropIfExists('product_categories');
    }

    public function down(): void
    {
        // Removed modules and their content are intentionally not recreated.
    }

    private function removeSeoPages(): void
    {
        if (! Schema::hasTable('seo_pages')) {
            return;
        }

        $query = DB::table('seo_pages')->whereIn('key', ['blog', 'shop']);
        $ids = $query->pluck('id');

        if ($ids->isNotEmpty() && Schema::hasTable('media')) {
            DB::table('media')
                ->where('model_type', 'App\\Models\\SeoPage')
                ->whereIn('model_id', $ids)
                ->delete();
        }

        $query->delete();
    }

    private function removeTranslationKeys(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $row = DB::table('site_settings')->where('key', 'translations')->first();

        if (! $row) {
            return;
        }

        $value = is_string($row->value)
            ? json_decode($row->value, true)
            : (array) $row->value;

        if (! is_array($value)) {
            return;
        }

        DB::table('site_settings')
            ->where('key', 'translations')
            ->update([
                'value' => json_encode(
                    $this->withoutRemovedKeys($value),
                    JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                ),
                'updated_at' => now(),
            ]);
    }

    private function withoutRemovedKeys(array $value): array
    {
        $clean = [];

        foreach ($value as $key => $nestedValue) {
            if (is_string($key) && $this->isRemovedKey($key)) {
                continue;
            }

            if (is_array($nestedValue)) {
                if ($this->isRemovedKey((string) ($nestedValue['key'] ?? ''))) {
                    continue;
                }

                $nestedValue = $this->withoutRemovedKeys($nestedValue);
            }

            $clean[$key] = $nestedValue;
        }

        return $clean;
    }

    private function isRemovedKey(string $key): bool
    {
        return $key === 'nav.blog'
            || $key === 'nav.shop'
            || str_starts_with($key, 'blog.')
            || str_starts_with($key, 'shop.');
    }
};
