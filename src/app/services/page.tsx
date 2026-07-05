import ServicesSchema from "@/components/seo/ServicesSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import WhySection from "@/sections/About/Why";
import CtaSection from "@/sections/Services/Cta/CtaSection";
import FaqSeqAction from "@/sections/Services/Faq/FaqSeqction";
import FeaturedSection from "@/sections/Services/Featured/FeaturedSection";
import HeroSection from "@/sections/Services/Hero/HeroSection";
import PartnerSection from "@/sections/Services/Partner/PartnerSection";
import ServiceSection from "@/sections/Services/Service/ServiceSection";
import WorkSection from "@/sections/Services/Work/WorkSection";

export async function generateMetadata() {
    const { branding } = await getSiteSettings();

    return createMetadata({
        title: "სერვისები",
        description:
            "CCTV, დაშვების კონტროლი, ქსელები, სერვერები და IT ინფრასტრუქტურული გადაწყვეტილებები.",
        path: "/services",
        keywords: [
            "CCTV",
            "დაშვების კონტროლი",
            "ქსელები",
            "სერვერები",
            "IT ინფრასტრუქტურა",
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
