import type { ServiceDetail } from "./model/types";
import ServiceViewTracker from "./components/ServiceViewTracker";
import BenefitsSection from "./sections/BenefitsSection";
import FaqSection from "./sections/FaqSection";
import IndustriesSection from "./sections/IndustriesSection";
import PartnersSection from "./sections/PartnersSection";
import ProcessSection from "./sections/ProcessSection";
import RelatedServices from "./sections/RelatedServices";
import ServiceCta from "./sections/ServiceCta";
import ServiceHero from "./sections/ServiceHero";
import ServiceOverview from "./sections/ServiceOverview";
import SolutionsSection from "./sections/SolutionsSection";

export default function ServiceDetailView({ service }: { service: ServiceDetail }) {
    return (
        <article className="pt-[76px]">
            <ServiceViewTracker serviceSlug={service.slug} />
            <ServiceHero service={service} />
            <ServiceOverview service={service} />
            <BenefitsSection service={service} />
            <SolutionsSection service={service} />
            <IndustriesSection service={service} />
            <ProcessSection service={service} />
            <PartnersSection />
            <FaqSection service={service} />
            <RelatedServices service={service} />
            <ServiceCta service={service} />
        </article>
    );
}
