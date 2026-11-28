<?php

use App\Models\LocalServiceLanding;
use Database\Seeders\PriorityLocalSeoCopy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->refreshHomepage(false);
        $this->refreshTbilisiCctv(false);
    }

    public function down(): void
    {
        $this->refreshHomepage(true);
        $this->refreshTbilisiCctv(true);
    }

    private function refreshHomepage(bool $reverse): void
    {
        if (! Schema::hasTable('site_settings')) {
            return;
        }

        $setting = DB::table('site_settings')->where('key', 'translations')->first();
        if (! $setting) {
            return;
        }

        $value = is_string($setting->value)
            ? json_decode($setting->value, true)
            : (array) $setting->value;
        if (! is_array($value['entries'] ?? null)) {
            return;
        }

        $pairs = [
            'home.hero.titlePrefix' => [
                'ka' => ['ტექნოლოგიური ინფრასტრუქტურა', 'კამერების მონტაჟი და სუსტი დენები'],
                'en' => ['Technology infrastructure', 'CCTV installation and low-voltage cabling'],
                'ru' => ['Технологическая инфраструктура', 'Монтаж камер и слаботочных сетей'],
            ],
            'home.hero.titleAccent' => [
                'ka' => ['თქვენი ობიექტისთვის', 'თბილისში და რეგიონებში'],
                'en' => ['built for your property', 'in Tbilisi and beyond'],
                'ru' => ['для вашего объекта', 'в Тбилиси и регионах'],
            ],
        ];

        // Update the two title segments together per language; preserve
        // customised CMS titles when either segment differs from the old copy.
        $indices = [];
        foreach ($value['entries'] as $index => $entry) {
            if (isset($pairs[$entry['key'] ?? ''])) {
                $indices[$entry['key']] = $index;
            }
        }
        if (count($indices) !== count($pairs)) {
            return;
        }

        $changed = false;
        $from = $reverse ? 1 : 0;
        $to = $reverse ? 0 : 1;
        foreach (['ka', 'en', 'ru'] as $locale) {
            foreach ($pairs as $key => $translations) {
                if (($value['entries'][$indices[$key]][$locale] ?? null) !== $translations[$locale][$from]) {
                    continue 2;
                }
            }
            foreach ($pairs as $key => $translations) {
                $value['entries'][$indices[$key]][$locale] = $translations[$locale][$to];
            }
            $changed = true;
        }

        if ($changed) {
            DB::table('site_settings')->where('key', 'translations')->update([
                'value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);
        }
    }

    private function refreshTbilisiCctv(bool $reverse): void
    {
        if (! Schema::hasTable('local_service_landings') || ! Schema::hasTable('services')) {
            return;
        }

        $city = PriorityLocalSeoCopy::cities()['tbilisi'];
        $technical = PriorityLocalSeoCopy::technical();
        $replacements = [];
        foreach (['ka', 'en', 'ru'] as $locale) {
            $replacements[$city['use']['security-access-automation'][$locale]] = $city['use']['tbilisi-cctv'][$locale];
            $replacements[$technical['security-access-automation'][$locale]] = $technical['tbilisi-cctv'][$locale];
        }
        if ($reverse) {
            $replacements = array_flip($replacements);
        }

        // Only replace known generic template sentences on the existing CCTV
        // Tbilisi landing. No reseeding, deletion, project relinking or changes
        // to publication state, canonical URLs, images, pricing or other cities.
        LocalServiceLanding::query()
            ->where('location_slug', 'tbilisi')
            ->whereHas('service', fn ($query) => $query->where('slug', 'security-camera-installation'))
            ->each(function (LocalServiceLanding $landing) use ($replacements): void {
                foreach (['excerpt', 'content', 'benefits', 'faq', 'seo_description', 'translations'] as $field) {
                    $original = $landing->{$field};
                    $updated = $this->replaceKnownCopy($original, $replacements);
                    if ($updated !== $original) {
                        $landing->{$field} = $updated;
                    }
                }
                if ($landing->isDirty()) {
                    $landing->save();
                }
            });
    }

    private function replaceKnownCopy(mixed $value, array $replacements): mixed
    {
        if (is_string($value)) {
            return strtr($value, $replacements);
        }
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->replaceKnownCopy($item, $replacements);
            }
        }

        return $value;
    }
};
