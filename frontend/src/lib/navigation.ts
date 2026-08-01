const basePrimaryNavigation = [
    {
        href: "/",
        key: "nav.home",
        fallback: { ka: "მთავარი", en: "Home", ru: "Главная" },
    },
    {
        href: "/about",
        key: "nav.about",
        fallback: { ka: "ჩვენ შესახებ", en: "About", ru: "О нас" },
    },
    {
        href: "/services",
        key: "nav.services",
        fallback: { ka: "სერვისები", en: "Services", ru: "Услуги" },
    },
    {
        href: "/projects",
        key: "nav.projects",
        fallback: { ka: "პროექტები", en: "Projects", ru: "Проекты" },
    },
    {
        href: "/contact",
        key: "nav.contact",
        fallback: { ka: "კონტაქტი", en: "Contact", ru: "Контакты" },
    },
] as const;

export function buildPrimaryNavigation() {
    return basePrimaryNavigation;
}

export function buildFooterNavigation() {
    return basePrimaryNavigation;
}

export const primaryNavigation = basePrimaryNavigation;
