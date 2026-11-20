<?php

namespace Database\Seeders;

use App\Models\LocalServiceLanding;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

final class TbilisiCctvLandingSeeder extends Seeder
{
    // Verified against the public project API on 2026-10-17 (Asia/Tbilisi).
    private const PROJECT_SLUGS = [
        '6-kameris-videometvalqureobis-sistema-meurneobis-obiektze-varketili',
        'videometvalqureobis-sistemis-aghdgena-gakhokidzis-kuchaze',
        'tbilisshi-tp-link-vigi-full-color-kamerebis-montazhi',
    ];

    public function run(): void
    {
        $landing = LocalServiceLanding::query()
            ->where('location_slug', 'tbilisi')
            ->whereHas('service', fn ($query) => $query->where('slug', 'security-camera-installation'))
            ->first();

        // Do not recreate deleted CMS records or overwrite subsequently edited copy.
        if (! $landing) {
            return;
        }

        if (str_contains((string) $landing->content, 'უფლებამოსილი პირების დაშვება')) {
            $backup = 'content-backups/2026-10-17-tbilisi-cctv-'.$landing->getKey().'.json';
            if (! Storage::disk('local')->exists($backup)) {
                $saved = Storage::disk('local')->put($backup, json_encode([
                    'attributes' => $landing->getRawOriginal(),
                    'project_ids' => $landing->projects()->pluck('projects.id')->all(),
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                if (! $saved) {
                    throw new \RuntimeException('Could not back up the Tbilisi CCTV landing.');
                }
            }

            $copy = json_decode(file_get_contents(database_path('content/tbilisi-cctv-20261017.json')), true, 512, JSON_THROW_ON_ERROR);
            $translations = $landing->translations ?? [];
            $fields = ['title', 'excerpt', 'content', 'ctaTitle', 'ctaText', 'seoTitle', 'seoDescription'];
            foreach (['ka', 'en', 'ru'] as $locale) {
                foreach ($fields as $field) {
                    data_set($translations, "fields.{$field}.{$locale}", $copy[$locale][$field]);
                }
                data_set($translations, "fields.ogTitle.{$locale}", $copy[$locale]['seoTitle']);
                data_set($translations, "fields.ogDescription.{$locale}", $copy[$locale]['seoDescription']);
            }

            $items = function (string $key, array $names) use ($copy): array {
                return collect($copy['ka'][$key])->map(function (array $values, int $index) use ($copy, $key, $names): array {
                    $item = array_combine($names, $values);
                    foreach (['en', 'ru'] as $locale) {
                        $item['translations'][$locale] = array_combine($names, $copy[$locale][$key][$index]);
                    }

                    return $item;
                })->all();
            };

            $landing->forceFill([
                'title' => $copy['ka']['title'], 'excerpt' => $copy['ka']['excerpt'],
                'content' => $copy['ka']['content'], 'cta_title' => $copy['ka']['ctaTitle'],
                'cta_text' => $copy['ka']['ctaText'], 'seo_title' => $copy['ka']['seoTitle'],
                'seo_description' => $copy['ka']['seoDescription'], 'translations' => $translations,
                'benefits' => $items('benefits', ['title', 'description']),
                'faq' => $items('faqs', ['question', 'answer']),
            ])->save();
        }

        $ids = Project::query()->publiclyVisible()
            ->whereIn('slug', self::PROJECT_SLUGS)
            ->whereIn('city', ['თბილისი', 'თბილისი, ვარკეთილი', 'Tbilisi'])
            ->get()
            ->reject(fn (Project $project): bool => (bool) data_get($project->seo, 'noindex', false))
            ->modelKeys();
        $changes = $landing->projects()->syncWithoutDetaching($ids);
        if ($changes['attached'] !== []) {
            $landing->touch();
        }
    }
}
