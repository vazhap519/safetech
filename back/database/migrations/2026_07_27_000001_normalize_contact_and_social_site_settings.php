<?php

use App\Support\SiteSettingValueNormalizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        foreach (['contact', 'socials'] as $key) {
            $record = DB::table('site_settings')->where('key', $key)->first();

            if (! $record) {
                continue;
            }

            $value = json_decode((string) $record->value, true);

            DB::table('site_settings')
                ->where('id', $record->id)
                ->update([
                    'value' => json_encode(
                        SiteSettingValueNormalizer::normalize($key, is_array($value) ? $value : []),
                        JSON_UNESCAPED_UNICODE,
                    ),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // The normalization is safe and does not need a destructive rollback.
    }
};
