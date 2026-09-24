<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TOP_GE_COUNTER_CODE = <<<'HTML'
<!-- TOP.GE ASYNC COUNTER CODE -->
<div id="top-ge-counter-container" data-site-id="118960"></div>
<script async src="//counter.top.ge/counter.js"></script>
<!-- / END OF TOP.GE COUNTER CODE -->
HTML;

    public function up(): void
    {
        $setting = DB::table('site_settings')->where('key', 'integrations')->first();
        $value = $this->decodeValue($setting?->value);

        if (! array_key_exists('footer_counter_code', $value)) {
            $value['footer_counter_code'] = self::TOP_GE_COUNTER_CODE;
        }

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'integrations'],
            [
                'group' => $setting?->group ?: 'general',
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'is_public' => true,
                'created_at' => $setting?->created_at ?: now(),
                'updated_at' => now(),
            ],
        );
    }

    public function down(): void
    {
        // Preserve administrator-managed footer counter code during rollbacks.
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
