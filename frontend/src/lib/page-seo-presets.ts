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
            ka: "IT ინფრასტრუქტურა და უსაფრთხოების სისტემები საქართველოში",
            en: "IT Infrastructure and Security Systems in Georgia",
            ru: "IT-инфраструктура и системы безопасности в Грузии",
        },
        description: {
            ka: "SafeTech ქმნის ვიდეოსამეთვალყურეობის, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალურ გადაწყვეტილებებს საქართველოში.",
            en: "SafeTech delivers professional CCTV, access control, networking, and server infrastructure solutions for businesses in Georgia.",
            ru: "SafeTech внедряет видеонаблюдение, контроль доступа, сетевую и серверную инфраструктуру для бизнеса в Грузии.",
        },
        keywords: ["CCTV Georgia", "IT infrastructure Georgia", "networking Georgia", "access control Georgia"],
    },
    about: {
        key: "about",
        path: "/about",
        title: {
            ka: "SafeTech-ის გუნდი და გამოცდილება საქართველოში",
            en: "SafeTech Team and Experience in Georgia",
            ru: "Команда и опыт SafeTech в Грузии",
        },
        description: {
            ka: "გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა საქართველოში უსაფრთხოებისა და IT ინფრასტრუქტურის პროექტებისადმი.",
            en: "Meet the SafeTech team and learn how we deliver security and IT infrastructure projects across Georgia.",
            ru: "Познакомьтесь с командой SafeTech и нашим подходом к проектам безопасности и IT-инфраструктуры в Грузии.",
        },
        keywords: ["SafeTech Georgia", "IT company Georgia", "systems integrator Georgia"],
    },
    services: {
        key: "services",
        path: "/services",
        title: {
            ka: "IT და უსაფრთხოების სერვისები საქართველოში",
            en: "IT and Security Services in Georgia",
            ru: "IT-услуги и системы безопасности в Грузии",
        },
        description: {
            ka: "ვიდეოსამეთვალყურეობა, დაშვების კონტროლი, ქსელები, სერვერები და მართვადი IT მხარდაჭერა ბიზნესისთვის საქართველოში.",
            en: "CCTV, access control, networking, server infrastructure, and managed IT support for businesses in Georgia.",
            ru: "Видеонаблюдение, контроль доступа, сети, серверная инфраструктура и IT-поддержка для бизнеса в Грузии.",
        },
        keywords: ["CCTV Georgia", "access control Georgia", "networking Georgia", "IT support Georgia"],
    },
    projects: {
        key: "projects",
        path: "/projects",
        title: {
            ka: "საქართველოში განხორციელებული IT და უსაფრთხოების პროექტები",
            en: "IT and Security Projects Delivered in Georgia",
            ru: "IT-проекты и системы безопасности в Грузии",
        },
        description: {
            ka: "ნახეთ SafeTech-ის მიერ საქართველოში განხორციელებული ვიდეოსამეთვალყურეობის, ქსელური და სერვერული ინფრასტრუქტურის პროექტები.",
            en: "Explore SafeTech CCTV, networking, and server infrastructure projects delivered for businesses across Georgia.",
            ru: "Проекты SafeTech по видеонаблюдению, сетевой и серверной инфраструктуре, реализованные в Грузии.",
        },
        keywords: ["IT projects Georgia", "CCTV projects Georgia", "network infrastructure Georgia"],
    },
    contact: {
        key: "contact",
        path: "/contact",
        title: {
            ka: "კონტაქტი და ტექნიკური კონსულტაცია საქართველოში",
            en: "Contact and Technical Consultation in Georgia",
            ru: "Контакты и техническая консультация в Грузии",
        },
        description: {
            ka: "დაუკავშირდით SafeTech-ს საქართველოში IT ინფრასტრუქტურისა და უსაფრთხოების სისტემების კონსულტაციისა და მორგებული შეთავაზებისთვის.",
            en: "Contact SafeTech in Georgia for an IT infrastructure or security systems consultation and a tailored proposal.",
            ru: "Свяжитесь с SafeTech в Грузии для консультации и предложения по IT-инфраструктуре и системам безопасности.",
        },
        keywords: ["SafeTech Georgia contact", "IT consultation Georgia", "security systems Georgia"],
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
