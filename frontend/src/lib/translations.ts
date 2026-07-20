import type { Locale } from "@/lib/locales";

type TranslationValues = Partial<Record<Locale, string>>;
export type TranslationMap = Record<string, TranslationValues>;

export type TranslationFallback =
    | string
    | Partial<Record<Locale, string>>
    | null
    | undefined;

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

function translationValues(entry: Record<string, unknown>): TranslationValues {
    return {
        ka: typeof entry.ka === "string" ? entry.ka.trim() : "",
        en: typeof entry.en === "string" ? entry.en.trim() : "",
        ru: typeof entry.ru === "string" ? entry.ru.trim() : "",
    };
}

export function buildTranslationMap(value: unknown): TranslationMap {
    if (isRecord(value) && Array.isArray(value.entries)) {
        return value.entries.reduce<TranslationMap>((accumulator, entry) => {
            if (!isRecord(entry) || typeof entry.key !== "string" || !entry.key.trim()) {
                return accumulator;
            }

            accumulator[entry.key.trim()] = translationValues(entry);

            return accumulator;
        }, {});
    }

    if (isRecord(value)) {
        return Object.entries(value).reduce<TranslationMap>(
            (accumulator, [key, entry]) => {
                if (!isRecord(entry)) return accumulator;

                accumulator[key] = translationValues(entry);

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
        return fallback.trim();
    }

    if (fallback && typeof fallback === "object") {
        return (
            fallback[locale]?.trim() ||
            fallback.ka?.trim() ||
            fallback.en?.trim() ||
            fallback.ru?.trim() ||
            ""
        );
    }

    return "";
}

export function hasTranslatedText(
    translations: TranslationMap,
    keys: string[],
    locale: Locale,
) {
    return keys.some((key) => Boolean(translations[key]?.[locale]?.trim()));
}

export function createTranslator(
    translations: TranslationMap,
    locale: Locale,
) {
    return (key: string, fallback: TranslationFallback) =>
        translateText(translations, key, locale, fallback);
}

type LocalizedItemDefinition = {
    icon: string;
    key: string;
};

export function translateKeyedItems(
    translations: TranslationMap,
    locale: Locale,
    items: readonly LocalizedItemDefinition[],
) {
    return items
        .map((item) => ({
            ...item,
            description: translateText(
                translations,
                `${item.key}.description`,
                locale,
                null,
            ),
            title: translateText(
                translations,
                `${item.key}.title`,
                locale,
                null,
            ),
        }))
        .filter((item) => item.title || item.description);
}

function translateIndexedItems(
    translations: TranslationMap,
    locale: Locale,
    prefix: string,
    icons: readonly string[],
) {
    return translateKeyedItems(
        translations,
        locale,
        icons.map((icon, index) => ({
            icon,
            key: `${prefix}.${index}`,
        })),
    );
}

export function translateIndexedSection(
    translations: TranslationMap,
    locale: Locale,
    prefix: string,
    icons: readonly string[],
) {
    return {
        title: translateText(translations, `${prefix}.title`, locale, null),
        description: translateText(
            translations,
            `${prefix}.description`,
            locale,
            null,
        ),
        items: translateIndexedItems(
            translations,
            locale,
            `${prefix}.item`,
            icons,
        ),
    };
}
