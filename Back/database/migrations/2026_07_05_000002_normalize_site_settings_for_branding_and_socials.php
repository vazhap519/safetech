<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $socials = DB::table('site_settings')->where('key', 'socials')->first();

        if ($socials) {
            $value = json_decode((string) $socials->value, true) ?: [];

            if (! isset($value['links']) && is_array($value)) {
                $links = [];

                foreach ($value as $network => $href) {
                    if (! is_string($network) || ! is_string($href) || blank($href)) {
                        continue;
                    }

                    $links[] = [
                        'network' => $network,
                        'label' => str($network)->replace('_', ' ')->title()->value(),
                        'href' => $href,
                    ];
                }

                DB::table('site_settings')
                    ->where('id', $socials->id)
                    ->update([
                        'value' => json_encode(['links' => $links], JSON_UNESCAPED_UNICODE),
                        'updated_at' => now(),
                    ]);
            }
        }

        $contact = DB::table('site_settings')->where('key', 'contact')->first();

        if ($contact) {
            $value = json_decode((string) $contact->value, true) ?: [];

            $value['hours'] ??= 'ორშ - პარ: 10:00 - 19:00';
            $value['whatsapp_message'] ??= 'გამარჯობა, მაინტერესებს SafeTech-ის მომსახურება.';
            $value['lead_email'] ??= 'safetechgeorgia@gmail.com';

            DB::table('site_settings')
                ->where('id', $contact->id)
                ->update([
                    'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
                    'updated_at' => now(),
                ]);
        }

        $seo = DB::table('site_settings')->where('key', 'seo')->first();
        $seoValue = $seo ? json_decode((string) $seo->value, true) ?: [] : [];
        $branding = DB::table('site_settings')->where('key', 'branding')->first();
        $brandingValue = $branding
            ? json_decode((string) $branding->value, true) ?: []
            : [];

        $normalizedBranding = [
            'site_name' => $brandingValue['site_name'] ?? $seoValue['site_name'] ?? 'SafeTech',
            'tagline' => $brandingValue['tagline'] ?? 'თქვენი ბიზნესის ტექნოლოგიური და უსაფრთხოების გარანტი.',
            'logo' => $brandingValue['logo'] ?? null,
            'footer_logo' => $brandingValue['footer_logo'] ?? null,
            'favicon' => $brandingValue['favicon'] ?? null,
            'default_image' => $brandingValue['default_image'] ?? $seoValue['default_image'] ?? null,
        ];

        DB::table('site_settings')->updateOrInsert(
            ['key' => 'branding'],
            [
                'group' => 'general',
                'value' => json_encode($normalizedBranding, JSON_UNESCAPED_UNICODE),
                'is_public' => true,
                'updated_at' => now(),
                'created_at' => $branding?->created_at ?? now(),
            ],
        );
    }

    public function down(): void
    {
        //
    }
};
