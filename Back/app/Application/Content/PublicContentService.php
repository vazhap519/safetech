<?php

namespace App\Application\Content;

use App\Models\Faq;
use App\Models\Partner;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Support\PublicContentCache;
use Illuminate\Support\Facades\Cache;

final class PublicContentService
{
    public function bootstrap(): array
    {
        return Cache::remember(PublicContentCache::key('bootstrap'), now()->addHour(), fn () => [
            'team' => TeamMember::query()->active()->get()->map(fn (TeamMember $member) => [
                'firstName' => $member->first_name, 'lastName' => $member->last_name,
                'position' => $member->position, 'image' => $member->image, 'bio' => $member->bio,
                'socials' => $member->socials ?? [],
            ]),
            'partners' => Partner::query()->active()->get()->map->only(['name', 'logo', 'url', 'category']),
            'testimonials' => Testimonial::query()->active()->get()->map->only(['quote', 'author', 'role', 'company', 'image']),
            'faqs' => Faq::query()->active()->whereNull('service_id')->get()->map->only(['question', 'answer', 'context']),
            'settings' => SiteSetting::query()->public()->get()->mapWithKeys(
                fn (SiteSetting $setting) => [$setting->key => $this->sanitizeSetting($setting)],
            ),
        ]);
    }

    private function sanitizeSetting(SiteSetting $setting): mixed
    {
        if ($setting->key !== 'contact' || ! is_array($setting->value)) {
            return $setting->value;
        }

        $value = $setting->value;
        unset($value['lead_email']);

        return $value;
    }
}
