import ServicesSchema from "@/components/seo/ServicesSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";
import WhySection from "@/sections/About/Why";
import CtaSection from "@/sections/Services/Cta/CtaSection";
import FaqSeqAction from "@/sections/Services/Faq/FaqSeqction";
import FeaturedSection from "@/sections/Services/Featured/FeaturedSection";
import HeroSection from "@/sections/Services/Hero/HeroSection";
import PartnerSection from "@/sections/Services/Partner/PartnerSection";
import ServiceSection from "@/sections/Services/Service/ServiceSection";
import WorkSection from "@/sections/Services/Work/WorkSection";

export async function generateMetadata() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return createMetadata({
        title: t("meta.services.title", {
            ka: "CCTV, ქსელები და IT სერვისები | SafeTech",
            en: "CCTV, Networking, and IT Services | SafeTech",
            ru: "CCTV, сети и IT-услуги | SafeTech",
        }),
        description: t("meta.services.description", {
            ka: "იხილეთ SafeTech-ის CCTV, დაშვების კონტროლის, ქსელური, სერვერული და სხვა IT ინფრასტრუქტურული სერვისები.",
            en: "Explore SafeTech CCTV, access control, networking, server, and other IT infrastructure services.",
            ru: "Изучите услуги SafeTech: видеонаблюдение, контроль доступа, сети, серверы и другая IT-инфраструктура.",
        }),
        path: "/services",
        locale,
        keywords: [
            "CCTV",
            "access control",
            "networking",
            "servers",
            "IT infrastructure",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

export default function Services() {
    return (
        <div>
            <ServicesSchema />
            <HeroSection />
            <PartnerSection />
            <ServiceSection />
            <FeaturedSection />
            <WhySection />
            <WorkSection />
            <FaqSeqAction />
            <CtaSection />
        </div>
    );
}
