import ServicesSchema from "@/components/seo/ServicesSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import WhySection from "@/sections/About/Why";
import CtaSection from "@/sections/Services/Cta/CtaSection";
import FaqSeqAction from "@/sections/Services/Faq/FaqSeqction";
import FeaturedSection from "@/sections/Services/Featured/FeaturedSection";
import HeroSection from "@/sections/Services/Hero/HeroSection";
import PartnerSection from "@/sections/Services/Partner/PartnerSection";
import ServiceSection from "@/sections/Services/Service/ServiceSection";
import WorkSection from "@/sections/Services/Work/WorkSection";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.services);
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
            <CmsPageSchema pageKey="services" fallback={<ServicesSchema />} />
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
