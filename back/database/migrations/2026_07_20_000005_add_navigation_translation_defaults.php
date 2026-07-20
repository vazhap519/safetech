<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $setting = DB::table('site_settings')->where('key', 'translations')->first();
        $value = $setting?->value;

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        $value = is_array($value) ? $value : [];
        $entries = is_array($value['entries'] ?? null) ? $value['entries'] : [];
        $existingKeys = array_fill_keys(
            array_filter(array_column($entries, 'key'), 'is_string'),
            true,
        );

        foreach ($this->defaults() as $entry) {
            if (! isset($existingKeys[$entry['key']])) {
                $entries[] = $entry;
            }
        }

        $value['entries'] = $entries;

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'translations'],
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
        // Preserve administrator-managed translation data during rollbacks.
    }

    /** @return array<int, array{key: string, ka: string, en: string, ru: string}> */
    private function defaults(): array
    {
        return [
            ['key' => 'nav.blog', 'ka' => 'ბლოგი', 'en' => 'Blog', 'ru' => 'Блог'],
            ['key' => 'footer.company.title', 'ka' => 'კომპანია', 'en' => 'Company', 'ru' => 'Компания'],
            ['key' => 'footer.services.title', 'ka' => 'სერვისები', 'en' => 'Services', 'ru' => 'Услуги'],
            ['key' => 'footer.contact.title', 'ka' => 'კონტაქტი', 'en' => 'Contact', 'ru' => 'Контакты'],
            ['key' => 'footer.copy.rights', 'ka' => 'ყველა უფლება დაცულია.', 'en' => 'All rights reserved.', 'ru' => 'Все права защищены.'],
        ];
    }
};
