import ContactSchema from "@/components/seo/ContactSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";
import FaqSection from "@/sections/Contact/FaqSection";
import FinalSection from "@/sections/Contact/Final";
import Form from "@/sections/Contact/Form";
import Hero from "@/sections/Contact/Hero";
import Info from "@/sections/Contact/Info";
import Intro from "@/sections/Contact/intro";
import Support from "@/sections/Contact/Support";

export async function generateMetadata() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return createMetadata({
        title: t("meta.contact.title", {
            ka: "კონტაქტი და კონსულტაცია | SafeTech",
            en: "Contact and Consultation | SafeTech",
            ru: "Контакты и консультация | SafeTech",
        }),
        description: t("meta.contact.description", {
            ka: "დაგვიკავშირდით CCTV, ქსელური, სერვერული და უსაფრთხოების სისტემების პროექტებისთვის საქართველოში.",
            en: "Contact SafeTech for CCTV, networking, server, and security system projects in Georgia.",
            ru: "Свяжитесь с SafeTech по проектам видеонаблюдения, сетевой, серверной и охранной инфраструктуры в Грузии.",
        }),
        path: "/contact",
        locale,
        keywords: [
            "contact safetech",
            "SafeTech",
            "CCTV Georgia",
            "IT infrastructure Georgia",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

export default function Contact() {
    return (
        <>
            <ContactSchema />
            <Hero />
            <Intro />
            <Form />
            <Info />
            <Support />
            <FaqSection />
            <FinalSection />
        </>
    );
}
