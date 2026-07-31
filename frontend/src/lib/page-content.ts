import type { Locale } from "@/lib/locales";
import type { TranslationMap } from "@/lib/translations";

const PAGE_HEADING_KEYS: Record<string, readonly string[]> = {
    home: ["home.hero.titlePrefix", "home.hero.titleAccent"],
    about: ["about.hero.title"],
    services: [
        "services.hero.titlePrefix",
        "services.hero.titleAccent",
        "services.hero.titleSuffix",
    ],
    projects: ["projects.hero.title"],
    contact: ["contact.hero.title"],
};

export function hasConfiguredPageHeading(
    translations: TranslationMap,
    pageKey: string,
    locale: Locale,
) {
    const keys = PAGE_HEADING_KEYS[pageKey] ?? [];

    return keys.some((key) => Boolean(translations[key]?.[locale]?.trim()));
}
