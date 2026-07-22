<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const GOOGLE_ANALYTICS_ID = 'G-VC9XHNPEG5';

    public function up(): void
    {
        $setting = DB::table('site_settings')->where('key', 'integrations')->first();
        $value = $this->decodeValue($setting?->value);

        if (blank($value['google_analytics_id'] ?? null)) {
            $value['google_analytics_id'] = self::GOOGLE_ANALYTICS_ID;
            $value['marketing_enabled'] = true;
        }

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'integrations'],
            [
                'group' => $setting?->group ?: 'general',
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
                'is_public' => true,
                'created_at' => $setting?->created_at ?: now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        // Preserve administrator-managed analytics data during rollbacks.
    }

    /** @return array<string, mixed> */
    private function decodeValue(mixed $value): array
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return is_array($value) ? $value : [];
    }
};
