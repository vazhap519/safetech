export const supportedLocales = ["ka", "en", "ru"] as const;
export const LOCALE_COOKIE_NAME = "safetech_locale";
export const DEFAULT_LOCALE = "ka";

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

export function isSupportedLocale(value?: string | null): value is Locale {
    return supportedLocales.includes((value || "").toLowerCase() as Locale);
}

export function isNonDefaultLocale(value?: string | null): value is Locale {
    if (!value) return false;

    const locale = normalizeLocale(value);

    return locale !== DEFAULT_LOCALE && supportedLocales.includes(locale);
}

function splitPathParts(path: string) {
    const [pathnameWithQuery = "/", hash = ""] = path.split("#", 2);
    const [pathname = "/", search = ""] = pathnameWithQuery.split("?", 2);

    return {
        pathname: pathname || "/",
        search: search ? `?${search}` : "",
        hash: hash ? `#${hash}` : "",
    };
}

export function stripLocalePrefix(path: string): string {
    const { pathname, search, hash } = splitPathParts(path);
    const segments = pathname.split("/").filter(Boolean);
    const firstSegment = segments[0];

    if (isNonDefaultLocale(firstSegment)) {
        const nextPath = `/${segments.slice(1).join("/")}`;
        return `${nextPath === "/" ? "/" : nextPath}${search}${hash}`;
    }

    if (firstSegment === DEFAULT_LOCALE) {
        const nextPath = `/${segments.slice(1).join("/")}`;
        return `${nextPath === "/" ? "/" : nextPath}${search}${hash}`;
    }

    return `${pathname}${search}${hash}`;
}

export function localizePath(path: string, locale: Locale): string {
    if (!path.startsWith("/")) {
        return path;
    }

    const normalizedPath = stripLocalePrefix(path);

    if (locale === DEFAULT_LOCALE) {
        return normalizedPath;
    }

    return normalizedPath === "/"
        ? `/${locale}`
        : `/${locale}${normalizedPath}`;
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

export function getLanguageTag(locale: Locale) {
    switch (locale) {
        case "en":
            return "en-US";
        case "ru":
            return "ru-RU";
        default:
            return "ka-GE";
    }
}
