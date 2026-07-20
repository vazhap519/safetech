export const primaryNavigation = [
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
        href: "/service-calculator",
        key: "nav.calculator",
        fallback: {
            ka: "კალკულატორი",
            en: "Calculator",
            ru: "Калькулятор",
        },
    },
    {
        href: "/projects",
        key: "nav.projects",
        fallback: { ka: "პროექტები", en: "Projects", ru: "Проекты" },
    },
    {
        href: "/blog",
        key: "nav.blog",
        fallback: { ka: "ბლოგი", en: "Blog", ru: "Блог" },
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
