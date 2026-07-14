import type { Locale } from "@/lib/locales";

export type TranslationValues = Partial<Record<Locale, string>>;
export type TranslationMap = Record<string, TranslationValues>;

export type TranslationFallback =
    | string
    | Partial<Record<Locale, string>>
    | null
    | undefined;

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

export function buildTranslationMap(value: unknown): TranslationMap {
    if (isRecord(value) && Array.isArray(value.entries)) {
        return value.entries.reduce<TranslationMap>((accumulator, entry) => {
            if (!isRecord(entry) || typeof entry.key !== "string" || !entry.key.trim()) {
                return accumulator;
            }

            accumulator[entry.key.trim()] = {
                ka: typeof entry.ka === "string" ? entry.ka.trim() : "",
                en: typeof entry.en === "string" ? entry.en.trim() : "",
                ru: typeof entry.ru === "string" ? entry.ru.trim() : "",
            };

            return accumulator;
        }, {});
    }

    if (isRecord(value)) {
        return Object.entries(value).reduce<TranslationMap>(
            (accumulator, [key, entry]) => {
                if (!isRecord(entry)) return accumulator;

                accumulator[key] = {
                    ka: typeof entry.ka === "string" ? entry.ka.trim() : "",
                    en: typeof entry.en === "string" ? entry.en.trim() : "",
                    ru: typeof entry.ru === "string" ? entry.ru.trim() : "",
                };

                return accumulator;
            },
            {},
        );
    }

    return {};
}

export function translateText(
    translations: TranslationMap,
    key: string,
    locale: Locale,
    fallback: TranslationFallback,
) {
    const configured = translations[key]?.[locale]?.trim();

    if (configured) {
        return configured;
    }

    if (typeof fallback === "string") {
        return fallback;
    }

    if (!isRecord(fallback)) {
        return "";
    }

    return (
        (typeof fallback[locale] === "string" ? fallback[locale] : "") ||
        (typeof fallback.ka === "string" ? fallback.ka : "") ||
        (typeof fallback.en === "string" ? fallback.en : "") ||
        (typeof fallback.ru === "string" ? fallback.ru : "") ||
        ""
    );
}

export function createTranslator(
    translations: TranslationMap,
    locale: Locale,
) {
    return (key: string, fallback: TranslationFallback) =>
        translateText(translations, key, locale, fallback);
}
