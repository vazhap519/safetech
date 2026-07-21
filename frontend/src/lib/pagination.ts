import type { Locale } from "@/lib/locales";

export function firstSearchParam(value: unknown): string {
    const candidate = Array.isArray(value) ? value[0] : value;

    return typeof candidate === "string" ? candidate.trim() : "";
}

export function parsePageNumber(value: unknown): number | null {
    const candidate = firstSearchParam(value);

    if (!/^[1-9]\d*$/.test(candidate)) return null;

    const page = Number(candidate);

    return Number.isSafeInteger(page) ? page : null;
}

export function paginatedTitle(subject: string, page: number, locale: Locale) {
    const pageLabel = locale === "en" ? "Page" : locale === "ru" ? "Страница" : "გვერდი";

    return `${subject} - ${pageLabel} ${page}`;
}

export function paginatedDescription(
    subject: string,
    page: number,
    locale: Locale,
) {
    switch (locale) {
        case "en":
            return `${subject}, page ${page}. Browse the published articles and practical resources in this section.`;
        case "ru":
            return `${subject}, страница ${page}. Ознакомьтесь с опубликованными статьями и практическими материалами раздела.`;
        default:
            return `${subject}, გვერდი ${page}. გაეცანით ამ განყოფილებაში გამოქვეყნებულ სტატიებსა და პრაქტიკულ მასალებს.`;
    }
}

export function invalidPaginationCopy(locale: Locale) {
    switch (locale) {
        case "en":
            return {
                title: "Page not found",
                description: "The requested paginated content page does not exist.",
            };
        case "ru":
            return {
                title: "Страница не найдена",
                description: "Запрошенная страница списка материалов не существует.",
            };
        default:
            return {
                title: "გვერდი ვერ მოიძებნა",
                description: "მოთხოვნილი გვერდი მასალების ჩამონათვალში არ არსებობს.",
            };
    }
}
