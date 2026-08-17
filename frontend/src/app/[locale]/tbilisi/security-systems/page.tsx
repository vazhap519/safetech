import type { Metadata } from "next";

import TbilisiSecuritySystemsLanding from "@/components/seo/TbilisiSecuritySystemsLanding";
import { normalizeLocale, type Locale } from "@/lib/locales";

type LocalizedPageProps = {
    params: Promise<{ locale: string }>;
};

const META: Record<Locale, { title: string; description: string; keywords: string[] }> = {
    ka: {
        title: "უსაფრთხოების კამერების მონტაჟი თბილისში | SafeTech",
        description: "უსაფრთხოების კამერების, ვიდეოსამეთვალყურეობის, დაშვების კონტროლის, დომოფონის, სიგნალიზაციის, ქსელებისა და IT ინფრასტრუქტურის მონტაჟი თბილისში.",
        keywords: ["უსაფრთხოების კამერების მონტაჟი თბილისში", "ვიდეოსამეთვალყურეობა თბილისი", "უსაფრთხოების სისტემები თბილისი"],
    },
    en: {
        title: "CCTV & Security System Installation in Tbilisi | SafeTech",
        description: "CCTV, access control, video intercom, alarm, structured cabling, Wi-Fi and IT infrastructure installation for homes and businesses in Tbilisi.",
        keywords: ["CCTV installation Tbilisi", "security cameras Tbilisi", "security systems Tbilisi", "access control Tbilisi"],
    },
    ru: {
        title: "Монтаж видеонаблюдения и систем безопасности в Тбилиси | SafeTech",
        description: "Монтаж камер, видеонаблюдения, контроля доступа, домофонов, сигнализации, сетей и IT-инфраструктуры для домов и бизнеса в Тбилиси.",
        keywords: ["видеонаблюдение Тбилиси", "установка камер Тбилиси", "системы безопасности Тбилиси", "контроль доступа Тбилиси"],
    },
};

export async function generateMetadata({ params }: LocalizedPageProps): Promise<Metadata> {
    const { locale: rawLocale } = await params;
    const locale = normalizeLocale(rawLocale);
    const meta = META[locale];
    const path = locale === "ka" ? "/tbilisi/security-systems" : `/${locale}/tbilisi/security-systems`;

    return {
        title: meta.title,
        description: meta.description,
        keywords: meta.keywords,
        alternates: {
            canonical: path,
            languages: {
                "ka-GE": "/tbilisi/security-systems",
                "en-GE": "/en/tbilisi/security-systems",
                "ru-GE": "/ru/tbilisi/security-systems",
            },
        },
        openGraph: {
            title: meta.title,
            description: meta.description,
            type: "website",
            locale: locale === "en" ? "en_GE" : locale === "ru" ? "ru_GE" : "ka_GE",
            url: path,
        },
    };
}

export default async function LocalizedTbilisiSecuritySystemsPage({ params }: LocalizedPageProps) {
    const { locale } = await params;

    return <TbilisiSecuritySystemsLanding locale={normalizeLocale(locale)} />;
}
