export const supportedLocales = ["ka", "en", "ru"] as const;
export const LOCALE_COOKIE_NAME = "safetech_locale";

export type Locale = (typeof supportedLocales)[number];

export const localeLabels: Record<Locale, string> = {
    ka: "ქართული",
    en: "English",
    ru: "Русский",
};

export function normalizeLocale(value?: string | null): Locale {
    if (!value) return "ka";

    const candidate = value.toLowerCase();

    if (candidate.startsWith("en")) return "en";
    if (candidate.startsWith("ru")) return "ru";

    return "ka";
}

export function getOgLocale(locale: Locale) {
    switch (locale) {
        case "en":
            return "en_US";
        case "ru":
            return "ru_RU";
        default:
            return "ka_GE";
    }
}
