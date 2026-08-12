import type { TranslationFallback } from "@/lib/translations";

export type PageSeoPreset = {
    key: string;
    translationKey?: string;
    path: string;
    title: TranslationFallback;
    description: TranslationFallback;
    keywords: string[];
    type?: "website" | "article";
    noindex?: boolean;
};

export const PAGE_SEO_PRESETS = {
    home: {
        key: "home",
        path: "/",
        title: {
            ka: "უსაფრთხოების კამერების მონტაჟი და IT ინფრასტრუქტურა საქართველოში",
            en: "CCTV Installation and IT Infrastructure in Georgia",
            ru: "Монтаж видеонаблюдения и IT-инфраструктура в Грузии",
        },
        description: {
            ka: "SafeTech გთავაზობთ უსაფრთხოების კამერების მონტაჟს, დაშვების კონტროლს, სიგნალიზაციას, ქსელურ და სერვერულ ინფრასტრუქტურას თბილისში და საქართველოს რეგიონებში.",
            en: "SafeTech provides CCTV installation, access control, alarm systems, networking, and server infrastructure in Tbilisi and across Georgia.",
            ru: "SafeTech выполняет монтаж видеонаблюдения, систем контроля доступа и сигнализации, а также сетевой и серверной инфраструктуры в Тбилиси и по Грузии.",
        },
        keywords: [
            "CCTV installation Georgia",
            "security cameras Tbilisi",
            "IT infrastructure Georgia",
            "access control Georgia",
        ],
    },
    about: {
        key: "about",
        path: "/about",
        title: {
            ka: "SafeTech — უსაფრთხოების სისტემებისა და IT ინფრასტრუქტურის გუნდი",
            en: "SafeTech Security Systems and IT Infrastructure Team",
            ru: "SafeTech — команда по системам безопасности и IT-инфраструктуре",
        },
        description: {
            ka: "გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა ვიდეოსამეთვალყურეობის, უსაფრთხოების სისტემებისა და IT ინფრასტრუქტურის პროექტებისადმი საქართველოში.",
            en: "Meet the SafeTech team and learn how we deliver CCTV, security systems, and IT infrastructure projects across Georgia.",
            ru: "Познакомьтесь с командой SafeTech и нашим подходом к видеонаблюдению, системам безопасности и IT-инфраструктуре в Грузии.",
        },
        keywords: ["SafeTech Georgia", "security systems company Georgia", "IT infrastructure team Georgia"],
    },
    services: {
        key: "services",
        path: "/services",
        title: {
            ka: "უსაფრთხოების სისტემების მონტაჟი და IT მომსახურება საქართველოში",
            en: "Security System Installation and IT Services in Georgia",
            ru: "Монтаж систем безопасности и IT-услуги в Грузии",
        },
        description: {
            ka: "შეარჩიეთ SafeTech-ის სერვისი: ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, სიგნალიზაცია, ქსელები, სერვერები და IT მხარდაჭერა ბიზნესისა და საცხოვრებელი ობიექტებისთვის.",
            en: "Choose a SafeTech service: CCTV, access control, alarms, networking, servers, and IT support for businesses and residential properties.",
            ru: "Услуги SafeTech: видеонаблюдение, контроль доступа, сигнализация, сети, серверы и IT-поддержка для бизнеса и жилых объектов.",
        },
        keywords: [
            "CCTV installation Tbilisi",
            "security systems Georgia",
            "access control Georgia",
            "IT support Georgia",
        ],
    },
    projects: {
        key: "projects",
        path: "/projects",
        title: {
            ka: "უსაფრთხოების სისტემებისა და IT ინფრასტრუქტურის პროექტები საქართველოში",
            en: "Security Systems and IT Infrastructure Projects in Georgia",
            ru: "Проекты систем безопасности и IT-инфраструктуры в Грузии",
        },
        description: {
            ka: "ნახეთ SafeTech-ის რეალური პროექტები: უსაფრთხოების კამერები, ქსელური ინფრასტრუქტურა, დაშვების კონტროლი და სხვა ტექნიკური გადაწყვეტილებები საქართველოში.",
            en: "Explore real SafeTech projects covering CCTV, network infrastructure, access control, and other technical solutions delivered in Georgia.",
            ru: "Посмотрите реализованные SafeTech проекты по видеонаблюдению, сетям, контролю доступа и другим техническим решениям в Грузии.",
        },
        keywords: ["CCTV projects Georgia", "security installation projects Georgia", "network infrastructure Georgia"],
    },
    contact: {
        key: "contact",
        path: "/contact",
        title: {
            ka: "უსაფრთხოების სისტემების კონსულტაცია და შეკვეთა | SafeTech",
            en: "Security Systems Consultation and Quote | SafeTech",
            ru: "Консультация и расчет систем безопасности | SafeTech",
        },
        description: {
            ka: "დაუკავშირდით SafeTech-ს უსაფრთხოების კამერების, დაშვების კონტროლის, ქსელური ან IT ინფრასტრუქტურის კონსულტაციისა და თქვენს ობიექტზე მორგებული შეთავაზებისთვის.",
            en: "Contact SafeTech for CCTV, access control, networking, or IT infrastructure consultation and a proposal tailored to your site.",
            ru: "Свяжитесь с SafeTech для консультации по видеонаблюдению, контролю доступа, сетям или IT-инфраструктуре и расчета под ваш объект.",
        },
        keywords: ["SafeTech contact", "CCTV quote Georgia", "security systems consultation Georgia"],
    },
    privacy: {
        key: "privacy",
        path: "/privacy",
        title: {
            ka: "კონფიდენციალურობის პოლიტიკა",
            en: "Privacy Policy",
            ru: "Политика конфиденциальности",
        },
        description: {
            ka: "SafeTech-ის კონფიდენციალურობის პოლიტიკა განმარტავს, როგორ ვაგროვებთ, ვიყენებთ და ვიცავთ მომხმარებლის მონაცემებს.",
            en: "The SafeTech privacy policy explains how we collect, use, and protect customer information.",
            ru: "Политика конфиденциальности SafeTech объясняет, как мы собираем, используем и защищаем данные клиентов.",
        },
        keywords: ["SafeTech privacy policy", "personal data Georgia", "website privacy"],
        noindex: true,
    },
} as const satisfies Record<string, PageSeoPreset>;

export type PageSeoPresetKey = keyof typeof PAGE_SEO_PRESETS;
