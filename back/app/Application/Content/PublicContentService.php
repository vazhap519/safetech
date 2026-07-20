<?php

namespace App\Application\Content;

use App\Models\Faq;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\MultilingualContent;
use App\Support\PublicContentCache;
use Illuminate\Support\Facades\Cache;

final class PublicContentService
{
    public function bootstrap(): array
    {
        return Cache::remember(PublicContentCache::key('bootstrap'), now()->addHour(), function (): array {
            $settings = SiteSetting::query()->public()->get()->mapWithKeys(
                fn (SiteSetting $setting) => [$setting->key => $this->sanitizeSetting($setting)],
            )->all();

            $settings['translations'] = $this->buildTranslations($settings['translations'] ?? null);

            return [
                'team' => TeamMember::query()->active()->get()->map(fn (TeamMember $member) => [
                    'id' => $member->id,
                    'firstName' => $member->first_name, 'lastName' => $member->last_name,
                    'position' => $member->position, 'image' => $member->image, 'bio' => $member->bio,
                    'socials' => $member->socials ?? [],
                ])->values()->all(),
                'partners' => Partner::query()->active()->get()->map->only(['name', 'logo', 'url', 'category'])->values()->all(),
                'testimonials' => Testimonial::query()->active()->get()->map->only(['id', 'quote', 'author', 'role', 'company', 'image'])->values()->all(),
                'faqs' => Faq::query()->active()->whereNull('service_id')->get()->map->only(['id', 'question', 'answer', 'context'])->values()->all(),
                'settings' => $settings,
            ];
        });
    }

    private function sanitizeSetting(SiteSetting $setting): mixed
    {
        if ($setting->key === 'branding' && is_array($setting->value)) {
            $value = $setting->value;

            foreach (['logo', 'footer_logo', 'favicon', 'default_image'] as $collection) {
                $value[$collection] = $setting->brandingMediaUrl($collection)
                    ?: ($value[$collection] ?? null);
            }

            return $value;
        }

        if ($setting->key !== 'contact' || ! is_array($setting->value)) {
            return $setting->value;
        }

        $value = $setting->value;
        unset($value['lead_email']);

        return $value;
    }

    private function buildTranslations(mixed $configuredTranslations): array
    {
        $map = MultilingualContent::mapFrom($configuredTranslations);

        Service::query()
            ->published()
            ->with('faqs')
            ->get()
            ->each(function (Service $service) use (&$map): void {
                $prefix = "service.{$service->slug}";

                MultilingualContent::mergeModelFields($map, $prefix, $service, [
                    'name' => 'name',
                    'eyebrow' => 'eyebrow',
                    'title' => 'title',
                    'description' => 'description',
                    'seoTitle' => fn (Service $model) => data_get($model->seo, 'title', $model->title),
                    'seoDescription' => 'seo_description',
                    'card.title' => fn (Service $model) => $model->name ?: $model->title,
                    'card.description' => 'description',
                ]);

                $service->faqs->values()->each(function (Faq $faq, int $index) use (&$map, $prefix): void {
                    MultilingualContent::mergeModelFields($map, "{$prefix}.faq.{$index}", $faq, [
                        'question' => 'question',
                        'answer' => 'answer',
                    ]);
                });
            });

        Project::query()
            ->published()
            ->get()
            ->each(function (Project $project) use (&$map): void {
                $prefix = "project.{$project->slug}";

                MultilingualContent::mergeModelFields($map, $prefix, $project, [
                    'name' => 'name',
                    'title' => 'title',
                    'description' => 'description',
                    'seoTitle' => fn (Project $model) => data_get($model->seo, 'title', $model->title),
                    'seoDescription' => 'seo_description',
                    'imageAlt' => 'image_alt',
                    'technology' => 'technology',
                    'card.title' => fn (Project $model) => $model->name ?: $model->title,
                    'card.description' => 'description',
                    'featured.title' => fn (Project $model) => $model->name ?: $model->title,
                    'featured.category' => fn (Project $model) => data_get($model->meta, '0.value', $model->category),
                    'featured.imageAlt' => fn (Project $model) => $model->image_alt ?: $model->name,
                ]);
            });

        TeamMember::query()
            ->active()
            ->get()
            ->each(fn (TeamMember $member) => MultilingualContent::mergeModelFields($map, "team.{$member->id}", $member, [
                'firstName' => 'first_name',
                'lastName' => 'last_name',
                'position' => 'position',
                'bio' => 'bio',
            ]));

        Testimonial::query()
            ->active()
            ->get()
            ->each(fn (Testimonial $testimonial) => MultilingualContent::mergeModelFields($map, "testimonial.{$testimonial->id}", $testimonial, [
                'quote' => 'quote',
                'author' => 'author',
                'role' => 'role',
                'company' => 'company',
            ]));

        Faq::query()
            ->active()
            ->whereNull('service_id')
            ->get()
            ->each(fn (Faq $faq) => MultilingualContent::mergeModelFields($map, "faq.{$faq->id}", $faq, [
                'question' => 'question',
                'answer' => 'answer',
            ]));

        return ['entries' => MultilingualContent::entriesFromMap($map)];
    }
}
