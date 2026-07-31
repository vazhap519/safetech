const basePrimaryNavigation = [
    {
        href: "/",
        key: "nav.home",
        fallback: { ka: "მთავარი", en: "Home", ru: "Главная" },
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
        href: "/shop",
        key: "nav.shop",
        fallback: { ka: "მაღაზია", en: "Shop", ru: "Магазин" },
    },
    {
        href: "/about",
        key: "nav.about",
        fallback: { ka: "ჩვენ შესახებ", en: "About", ru: "О нас" },
    },
    {
        href: "/contact",
        key: "nav.contact",
        fallback: { ka: "კონტაქტი", en: "Contact", ru: "Контакты" },
    },
] as const;

export const calculatorNavigationItem = {
    href: "/service-calculator",
    key: "nav.calculator",
    fallback: {
        ka: "კალკულატორი",
        en: "Calculator",
        ru: "Калькулятор",
    },
} as const;

export function buildPrimaryNavigation(showShop = true) {
    return showShop
        ? basePrimaryNavigation
        : basePrimaryNavigation.filter((item) => item.href !== "/shop");
}

export function buildFooterNavigation(showShop = true) {
    const navigation = buildPrimaryNavigation(showShop);
    const servicesIndex = navigation.findIndex((item) => item.href === "/services");

    return [
        ...navigation.slice(0, servicesIndex + 1),
        calculatorNavigationItem,
        ...navigation.slice(servicesIndex + 1),
    ];
}

export const primaryNavigation = buildPrimaryNavigation();
