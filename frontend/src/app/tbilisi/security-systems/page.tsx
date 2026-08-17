import type { Metadata } from "next";

import TbilisiSecuritySystemsLanding from "@/components/seo/TbilisiSecuritySystemsLanding";

const title = "უსაფრთხოების კამერების მონტაჟი თბილისში | SafeTech";
const description = "უსაფრთხოების კამერების, ვიდეოსამეთვალყურეობის, დაშვების კონტროლის, დომოფონის, სიგნალიზაციის, ქსელებისა და IT ინფრასტრუქტურის მონტაჟი თბილისში.";

export const metadata: Metadata = {
    title,
    description,
    keywords: [
        "უსაფრთხოების კამერების მონტაჟი თბილისში",
        "ვიდეოსამეთვალყურეობა თბილისი",
        "კამერების მონტაჟი თბილისი",
        "CCTV installation Tbilisi",
        "უსაფრთხოების სისტემები თბილისი",
        "დაშვების კონტროლი თბილისი",
    ],
    alternates: {
        canonical: "/tbilisi/security-systems",
        languages: {
            "ka-GE": "/tbilisi/security-systems",
            "en-GE": "/en/tbilisi/security-systems",
            "ru-GE": "/ru/tbilisi/security-systems",
        },
    },
    openGraph: {
        title,
        description,
        type: "website",
        locale: "ka_GE",
        url: "/tbilisi/security-systems",
    },
};

export default function TbilisiSecuritySystemsPage() {
    return <TbilisiSecuritySystemsLanding locale="ka" />;
}
