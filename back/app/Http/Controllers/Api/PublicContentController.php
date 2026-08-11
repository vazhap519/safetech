<?php

namespace App\Http\Controllers\Api;

use App\Application\Content\PublicContentService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PublicContentController extends Controller
{
    private const TRANSLATION_LOCALES = ['ka', 'en', 'ru'];

    public function __invoke(Request $request, PublicContentService $service): JsonResponse
    {
        $content = $service->bootstrap();
        $settings = is_array($content['settings'] ?? null)
            ? $content['settings']
            : [];
        $translationBundle = is_array($settings['translations'] ?? null)
            ? $settings['translations']
            : [];
        $entries = collect(
            is_array($translationBundle['entries'] ?? null)
                ? $translationBundle['entries']
                : [],
        )
            ->filter(fn (mixed $entry): bool => is_array($entry) && filled($entry['key'] ?? null))
            ->values();
        $locale = trim($request->string('translation_locale')->toString());

        if (in_array($locale, self::TRANSLATION_LOCALES, true)) {
            $translationBundle['entries'] = $entries
                ->map(function (array $entry) use ($locale): array {
                    $value = trim((string) ($entry[$locale] ?? ''));

                    return array_filter([
                        'key' => trim((string) $entry['key']),
                        $locale => $value,
                    ], fn (string $value): bool => $value !== '');
                })
                ->filter(fn (array $entry): bool => array_key_exists($locale, $entry))
                ->values()
                ->all();
        }

        $clientPrefixes = collect(
            explode(',', $request->string('client_translation_prefixes')->toString()),
        )
            ->map(fn (string $prefix): string => trim($prefix))
            ->filter(fn (string $prefix): bool => $prefix !== '' && mb_strlen($prefix) <= 80)
            ->filter(fn (string $prefix): bool => preg_match('/^[A-Za-z0-9_.-]+$/', $prefix) === 1)
            ->unique()
            ->take(32)
            ->values();

        if ($clientPrefixes->isNotEmpty()) {
            $settings['client_translations'] = [
                'entries' => $entries
                    ->filter(function (array $entry) use ($clientPrefixes): bool {
                        $key = trim((string) $entry['key']);

                        return $clientPrefixes->contains(
                            fn (string $prefix): bool => $key === $prefix || str_starts_with($key, "{$prefix}."),
                        );
                    })
                    ->values()
                    ->all(),
            ];
        }

        $settings['translations'] = $translationBundle;
        $content['settings'] = $settings;

        return response()->json(['data' => $content]);
    }
}
