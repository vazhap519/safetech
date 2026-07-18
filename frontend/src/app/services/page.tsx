import ServicesSchema from "@/components/seo/ServicesSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
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

    return createMetadata({
        title: translateText(translations, "meta.services.title", locale, null),
        description: translateText(
            translations,
            "meta.services.description",
            locale,
            null,
        ),
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

type ServicesPageProps = {
    searchParams?: Promise<{
        category?: string;
    }>;
};

export default async function Services({ searchParams }: ServicesPageProps) {
    const category = (await searchParams)?.category;

    return (
        <div>
            <ServicesSchema />
            <HeroSection />
            <PartnerSection />
            <ServiceSection category={category} />
            <FeaturedSection />
            <WhySection />
            <WorkSection />
            <FaqSeqAction />
            <CtaSection />
        </div>
    );
}
