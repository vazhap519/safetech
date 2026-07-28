import type { Locale } from "@/lib/locales";

type SearchParamValue = string | string[] | undefined;

export function firstSearchParamValue(value: SearchParamValue) {
    if (typeof value === "string") {
        return value.trim();
    }

    if (Array.isArray(value)) {
        return (value[0] || "").trim();
    }

    return "";
}

export function extractProductFilters(
    searchParams: Record<string, SearchParamValue> = {},
) {
    return Object.entries(searchParams).reduce<Record<string, string[]>>(
        (accumulator, [key, value]) => {
            if (!key.startsWith("filter_")) {
                return accumulator;
            }

            const filterSlug = key.slice(7).trim();
            const rawValues = Array.isArray(value)
                ? value.flatMap((item) => String(item).split(","))
                : String(value || "").split(",");
            const optionSlugs = rawValues
                .map((item) => item.trim())
                .filter(Boolean);

            if (filterSlug && optionSlugs.length) {
                accumulator[filterSlug] = [...new Set(optionSlugs)];
            }

            return accumulator;
        },
        {},
    );
}

function localeForIntl(locale: Locale) {
    switch (locale) {
        case "en":
            return "en-US";
        case "ru":
            return "ru-RU";
        default:
            return "ka-GE";
    }
}

export function formatProductPrice(
    value: number,
    currency: string,
    locale: Locale,
) {
    try {
        return new Intl.NumberFormat(localeForIntl(locale), {
            style: "currency",
            currency,
            maximumFractionDigits: Number.isInteger(value) ? 0 : 2,
        }).format(value);
    } catch {
        return `${value.toFixed(2)} ${currency}`.trim();
    }
}

export function plainTextFromHtml(value: string) {
    return value.replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
}
