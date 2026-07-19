<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings') || ! Schema::hasTable('site_settings')) {
            return;
        }

        $legacy = DB::table('settings')->orderBy('id')->first();

        if (! $legacy) {
            return;
        }

        $contact = $this->setting('contact');
        $seo = $this->setting('seo');
        $socials = $this->setting('socials');

        $this->fillBlank($contact, 'phone', $legacy->phone ?? null);
        $this->fillBlank($contact, 'email', $legacy->email ?? null);
        $this->fillBlank($contact, 'address', $legacy->address ?? null);

        $openTime = $legacy->open_time ?? null;
        $closeTime = $legacy->close_time ?? null;
        $hours = filled($openTime) && filled($closeTime) ? "{$openTime}-{$closeTime}" : null;
        $this->fillBlank($contact, 'hours', $hours);

        foreach (['city', 'country', 'lat', 'lng', 'open_time', 'close_time'] as $field) {
            $this->fillBlank($seo, $field, $legacy->{$field} ?? null);
        }

        $links = is_array($socials['links'] ?? null) ? $socials['links'] : [];
        $knownNetworks = collect($links)
            ->filter('is_array')
            ->pluck('network')
            ->filter()
            ->all();

        foreach (['facebook', 'instagram', 'linkedin'] as $network) {
            $href = $legacy->{$network} ?? null;

            if (blank($href) || in_array($network, $knownNetworks, true)) {
                continue;
            }

            $links[] = [
                'network' => $network,
                'label' => ucfirst($network),
                'href' => $href,
            ];
        }

        $socials['links'] = $links;
        $this->fillBlank($socials, 'share_title', $legacy->share_title ?? null);

        $legacyButtons = json_decode((string) ($legacy->share_buttons ?? ''), true);
        if (empty($socials['share_buttons']) && is_array($legacyButtons)) {
            $socials['share_buttons'] = collect($legacyButtons)
                ->map(fn (mixed $button): ?string => is_array($button)
                    ? ($button['type'] ?? null)
                    : (is_string($button) ? $button : null))
                ->filter()
                ->values()
                ->all();
        }

        $this->store('contact', $contact);
        $this->store('seo', $seo);
        $this->store('socials', $socials);
    }

    public function down(): void
    {
        // The legacy table is intentionally retained, so rollback needs no data rewrite.
    }

    /** @return array<string, mixed> */
    private function setting(string $key): array
    {
        $record = DB::table('site_settings')->where('key', $key)->first();
        $value = $record ? json_decode((string) $record->value, true) : [];

        return is_array($value) ? $value : [];
    }

    /** @param array<string, mixed> $value */
    private function store(string $key, array $value): void
    {
        DB::table('site_settings')->updateOrInsert(
            ['key' => $key],
            [
                'group' => 'general',
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE),
                'is_public' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
    }

    /** @param array<string, mixed> $target */
    private function fillBlank(array &$target, string $key, mixed $value): void
    {
        if (blank($target[$key] ?? null) && filled($value)) {
            $target[$key] = $value;
        }
    }
};
