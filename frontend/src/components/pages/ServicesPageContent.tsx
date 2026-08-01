import ServicesSchema from "@/components/seo/ServicesSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { getCurrentLocale } from "@/lib/locale-server";
import { firstSearchParam } from "@/lib/pagination";
import { localizeHref } from "@/lib/seo";
import { redirect } from "next/navigation";
import WhySection from "@/sections/About/Why";
import CtaSection from "@/sections/Services/Cta/CtaSection";
import FaqSeqAction from "@/sections/Services/Faq/FaqSeqction";
import FeaturedSection from "@/sections/Services/Featured/FeaturedSection";
import HeroSection from "@/sections/Services/Hero/HeroSection";
import PartnerSection from "@/sections/Services/Partner/PartnerSection";
import ServiceSection from "@/sections/Services/Service/ServiceSection";
import WorkSection from "@/sections/Services/Work/WorkSection";

type ServicesPageContentProps = {
    searchParams?:
        | Promise<{ category?: string; service?: string }>
        | { category?: string; service?: string };
    showPageSchema?: boolean;
};

export default async function ServicesPageContent({
    searchParams,
    showPageSchema = true,
}: ServicesPageContentProps) {
    const resolvedSearchParams = await searchParams;
    const category = firstSearchParam(resolvedSearchParams?.category);
    const selectedService = firstSearchParam(resolvedSearchParams?.service);

    if (showPageSchema && category) {
        const locale = await getCurrentLocale();
        const path = category === "all"
            ? "/services"
            : `/services/category/${encodeURIComponent(category)}`;

        redirect(localizeHref(path, locale));
    }

    return (
        <div>
            {showPageSchema ? (
                <CmsPageSchema pageKey="services" fallback={<ServicesSchema />} />
            ) : null}
            <HeroSection />
            <PartnerSection />
            <ServiceSection
                category={category || undefined}
                initialService={selectedService || undefined}
            />
            <FeaturedSection />
            <WhySection />
            <WorkSection />
            <FaqSeqAction />
            <CtaSection />
        </div>
    );
}
