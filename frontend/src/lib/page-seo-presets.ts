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
            ka: "კამერების მონტაჟი, ქსელები და IT მომსახურება საქართველოში | SafeTech",
            en: "CCTV Installation, Networking and IT Services in Georgia | SafeTech",
            ru: "Монтаж камер, сети и IT-услуги в Грузии | SafeTech",
        },
        description: {
            ka: "SafeTech — უსაფრთხოების კამერების მონტაჟი, ქსელის გაყვანა, Wi‑Fi, დაშვების კონტროლი, შლაგბაუმები, POS და IT მომსახურება თბილისში, ხაშურში და საქართველოს რეგიონებში.",
            en: "SafeTech provides CCTV installation, network cabling, Wi‑Fi, access control, barrier gates, POS and IT services in Tbilisi and across Georgia.",
            ru: "SafeTech выполняет монтаж видеонаблюдения, сетей, Wi‑Fi, контроля доступа, шлагбаумов, POS и IT-обслуживание в Тбилиси и по Грузии.",
        },
        keywords: [
            "კამერების მონტაჟი",
            "ვიდეოკამერების მონტაჟი",
            "უსაფრთხოების კამერები თბილისი",
            "ქსელის გაყვანა",
            "WiFi გამართვა",
            "IT მომსახურება",
            "შლაგბაუმის მონტაჟი",
            "CCTV installation Georgia",
            "security cameras Tbilisi",
            "IT support Georgia",
        ],
    },
    about: {
        key: "about",
        path: "/about",
        title: {
            ka: "SafeTech Georgia — უსაფრთხოების სისტემებისა და IT მომსახურების გუნდი",
            en: "SafeTech Georgia — Security Systems and IT Services Team",
            ru: "SafeTech Georgia — команда по системам безопасности и IT-услугам",
        },
        description: {
            ka: "გაიცანით SafeTech-ის გამოცდილება უსაფრთხოების კამერების, ქსელების, დაშვების კონტროლის, ავტომატიზაციისა და IT ინფრასტრუქტურის დაგეგმვა-მონტაჟში საქართველოში.",
            en: "Meet the SafeTech team delivering CCTV, networking, access control, automation and IT infrastructure projects across Georgia.",
            ru: "Познакомьтесь с SafeTech: видеонаблюдение, сети, контроль доступа, автоматизация и IT-инфраструктура по всей Грузии.",
        },
        keywords: [
            "SafeTech Georgia",
            "უსაფრთხოების სისტემების კომპანია",
            "IT კომპანია საქართველო",
            "security systems company Georgia",
            "IT infrastructure team Georgia",
        ],
    },
    services: {
        key: "services",
        path: "/services",
        title: {
            ka: "კამერების მონტაჟი, ქსელის გაყვანა და IT სერვისები | SafeTech",
            en: "CCTV Installation, Network Cabling and IT Services | SafeTech",
            ru: "Монтаж камер, прокладка сетей и IT-услуги | SafeTech",
        },
        description: {
            ka: "SafeTech-ის სერვისები: ვიდეოსამეთვალყურეობა, კამერების მონტაჟი, LAN/Wi‑Fi ქსელები, დაშვების კონტროლი, შლაგბაუმები, სიგნალიზაცია, სერვერები, POS და IT მხარდაჭერა.",
            en: "SafeTech services include CCTV installation, LAN/Wi‑Fi networking, access control, barrier gates, alarms, servers, POS and IT support.",
            ru: "Услуги SafeTech: видеонаблюдение, LAN/Wi‑Fi сети, контроль доступа, шлагбаумы, сигнализация, серверы, POS и IT-поддержка.",
        },
        keywords: [
            "კამერების მონტაჟი თბილისი",
            "ვიდეოსამეთვალყურეობა",
            "ქსელის მონტაჟი",
            "LAN კაბელის გაყვანა",
            "WiFi მონტაჟი",
            "IT support",
            "CCTV installation Tbilisi",
            "security systems Georgia",
            "access control Georgia",
        ],
    },
    projects: {
        key: "projects",
        path: "/projects",
        title: {
            ka: "შესრულებული კამერების, ქსელებისა და IT პროექტები | SafeTech Georgia",
            en: "Completed CCTV, Network and IT Projects | SafeTech Georgia",
            ru: "Реализованные проекты CCTV, сетей и IT | SafeTech Georgia",
        },
        description: {
            ka: "ნახეთ SafeTech-ის რეალური ნამუშევრები: უსაფრთხოების კამერები, PoE/NVR სისტემები, ქსელის გაყვანა, Wi‑Fi, დაშვების კონტროლი და IT ინფრასტრუქტურა საქართველოში.",
            en: "Explore real SafeTech work: CCTV, PoE/NVR systems, network cabling, Wi‑Fi, access control and IT infrastructure delivered in Georgia.",
            ru: "Реальные проекты SafeTech: видеонаблюдение, PoE/NVR, сети, Wi‑Fi, контроль доступа и IT-инфраструктура в Грузии.",
        },
        keywords: [
            "კამერების მონტაჟის პროექტები",
            "ვიდეოსამეთვალყურეობის მონტაჟი",
            "ქსელის პროექტები",
            "CCTV projects Georgia",
            "security installation projects Georgia",
            "network infrastructure Georgia",
        ],
    },
    contact: {
        key: "contact",
        path: "/contact",
        title: {
            ka: "კამერების მონტაჟის ფასი და უფასო კონსულტაცია | SafeTech",
            en: "CCTV Installation Quote and Consultation | SafeTech",
            ru: "Расчет монтажа камер и консультация | SafeTech",
        },
        description: {
            ka: "მიიღეთ SafeTech-ის კონსულტაცია და ინდივიდუალური შეთავაზება კამერების, ქსელის, Wi‑Fi, დაშვების კონტროლის, შლაგბაუმის, POS ან IT მომსახურებისთვის. დაგვიკავშირდით: 571 430 169 / 557 316 310.",
            en: "Request a tailored SafeTech quote for CCTV, networking, Wi‑Fi, access control, barrier gates, POS or IT services. Call 571 430 169 / 557 316 310.",
            ru: "Получите индивидуальный расчет SafeTech по камерам, сетям, Wi‑Fi, контролю доступа, шлагбаумам, POS или IT-услугам. Тел.: 571 430 169 / 557 316 310.",
        },
        keywords: [
            "კამერების მონტაჟის ფასი",
            "უსაფრთხოების კამერების ფასი",
            "IT მომსახურების ფასი",
            "SafeTech contact",
            "CCTV quote Georgia",
            "security systems consultation Georgia",
        ],
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
