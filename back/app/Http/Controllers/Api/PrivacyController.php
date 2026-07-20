<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use App\Support\MultilingualContent;
use App\Support\SafeHtml;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PrivacyController extends Controller
{
    public function __construct(private readonly SafeHtml $safeHtml) {}

    public function index(Request $request)
    {
        $locale = $this->locale($request);

        $privacy = Cache::remember("privacy_page:{$locale}", 300, function () use ($locale) {
            $privacy = PrivacyPolicy::query()->first();

            if (! $privacy) {
                return null;
            }

            return [
                'title' => $this->translated($privacy, 'title', $privacy->title, $locale),
                'highlight' => $this->translated($privacy, 'highlight', $privacy->highlight, $locale),
                'content' => $this->safeHtml->sanitize(
                    $this->translated($privacy, 'content', $privacy->content, $locale),
                ),
            ];
        });

        return response()->json($privacy ?? [
            'title' => '',
            'highlight' => '',
            'content' => '',
        ]);
    }

    private function locale(Request $request): string
    {
        $locale = $request->string('locale')->toString();

        return in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
    }

    private function translated(PrivacyPolicy $privacy, string $field, mixed $fallback, string $locale): string
    {
        $values = MultilingualContent::valuesForField($privacy, $field, $fallback);

        return $values[$locale] ?: (is_string($fallback) ? $fallback : '');
    }
}
