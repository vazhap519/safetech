<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

final class PublicContentEligibility
{
    /**
     * Kept for compatibility with legacy content models. The blog Post model
     * was removed from the core application, so depending on that concrete
     * class made merely loading this helper fail with a missing-class error.
     */
    public static function post(Model $post, string $locale = 'ka'): bool
    {
        if (! self::hasMeaningfulText(self::translated($post, 'title', $post->title, $locale))) {
            return false;
        }

        if (self::hasMeaningfulText(self::translated($post, 'excerpt', $post->excerpt, $locale))) {
            return true;
        }

        if (self::hasMeaningfulText(self::translated($post, 'body', $post->body, $locale))) {
            return true;
        }

        return $post->sections->contains(
            fn (Model $section): bool => self::hasMeaningfulText(
                self::translated($section, 'content', $section->getAttribute('content'), $locale),
            ),
        );
    }

    public static function hasMeaningfulText(mixed $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        $text = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\s\x{00A0}]+/u', ' ', $text) ?? '';

        return trim($text) !== '';
    }

    private static function translated(Model $model, string $field, mixed $fallback, string $locale): string
    {
        $locale = in_array($locale, MultilingualContent::LOCALES, true) ? $locale : 'ka';
        $values = MultilingualContent::valuesForField($model, $field, $fallback);

        return $values[$locale] ?: (is_string($fallback) ? $fallback : '');
    }
}
