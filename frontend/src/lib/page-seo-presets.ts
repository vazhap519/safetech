import type { TranslationFallback } from "@/lib/translations";

export type PageSeoPreset = {
    key: string;
    translationKey?: string;
    path: string;
    title: TranslationFallback;
    description: TranslationFallback;
    keywords: string[];
    type?: "website" | "article";
};

export const PAGE_SEO_PRESETS = {
    home: {
        key: "home",
        path: "/",
        title: {
            ka: "IT ინფრასტრუქტურა და უსაფრთხოების სისტემები ბიზნესისთვის",
            en: "IT Infrastructure and Security Systems for Business",
            ru: "IT-инфраструктура и системы безопасности для бизнеса",
        },
        description: {
            ka: "SafeTech ქმნის ვიდეოსამეთვალყურეობის, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალურ გადაწყვეტილებებს საქართველოში.",
            en: "SafeTech delivers professional CCTV, access control, networking, and server infrastructure solutions for businesses in Georgia.",
            ru: "SafeTech внедряет видеонаблюдение, контроль доступа, сетевую и серверную инфраструктуру для бизнеса в Грузии.",
        },
        keywords: ["CCTV", "IT infrastructure", "networking", "access control"],
    },
    about: {
        key: "about",
        path: "/about",
        title: {
            ka: "SafeTech-ის გუნდი და გამოცდილება",
            en: "SafeTech Team and Experience",
            ru: "Команда и опыт SafeTech",
        },
        description: {
            ka: "გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა უსაფრთხოებისა და IT ინფრასტრუქტურის პროექტებისადმი.",
            en: "Meet the SafeTech team and learn how we deliver security and IT infrastructure projects.",
            ru: "Познакомьтесь с командой SafeTech и нашим подходом к проектам безопасности и IT-инфраструктуры.",
        },
        keywords: ["SafeTech", "IT company Georgia", "systems integrator"],
    },
    services: {
        key: "services",
        path: "/services",
        title: {
            ka: "IT და უსაფრთხოების სერვისები",
            en: "IT and Security Services",
            ru: "IT-услуги и системы безопасности",
        },
        description: {
            ka: "ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, ქსელები, სერვერები და მართვადი IT მხარდაჭერა ბიზნესისთვის საქართველოში.",
            en: "CCTV, access control, networking, server infrastructure, and managed IT support for businesses in Georgia.",
            ru: "Видеонаблюдение, контроль доступа, сети, серверная инфраструктура и IT-поддержка для бизнеса в Грузии.",
        },
        keywords: ["CCTV", "access control", "networking", "IT support"],
    },
    projects: {
        key: "projects",
        path: "/projects",
        title: {
            ka: "განხორციელებული IT და უსაფრთხოების პროექტები",
            en: "Completed IT and Security Projects",
            ru: "Реализованные IT-проекты и системы безопасности",
        },
        description: {
            ka: "ნახეთ SafeTech-ის მიერ განხორციელებული ვიდეოსამეთვალყურეობის, ქსელური და სერვერული ინფრასტრუქტურის პროექტები.",
            en: "Explore SafeTech CCTV, networking, and server infrastructure projects delivered for businesses.",
            ru: "Проекты SafeTech по видеонаблюдению, сетевой и серверной инфраструктуре для бизнеса.",
        },
        keywords: ["IT projects", "CCTV projects", "network infrastructure"],
    },
    contact: {
        key: "contact",
        path: "/contact",
        title: {
            ka: "კონტაქტი და ტექნიკური კონსულტაცია",
            en: "Contact and Technical Consultation",
            ru: "Контакты и техническая консультация",
        },
        description: {
            ka: "დაუკავშირდით SafeTech-ს IT ინფრასტრუქტურისა და უსაფრთხოების სისტემების კონსულტაციისა და მორგებული შეთავაზებისთვის.",
            en: "Contact SafeTech for an IT infrastructure or security systems consultation and a tailored proposal.",
            ru: "Свяжитесь с SafeTech для консультации и предложения по IT-инфраструктуре и системам безопасности.",
        },
        keywords: ["SafeTech contact", "IT consultation", "security systems proposal"],
    },
} as const satisfies Record<string, PageSeoPreset>;

export type PageSeoPresetKey = keyof typeof PAGE_SEO_PRESETS;
