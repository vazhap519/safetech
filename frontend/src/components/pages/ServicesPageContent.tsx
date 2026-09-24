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
import LocalizedLink from "@/components/ui/LocalizedLink";
import PartnerSection from "@/sections/Services/Partner/PartnerSection";
import ServiceSection from "@/sections/Services/Service/ServiceSection";
import WorkSection from "@/sections/Services/Work/WorkSection";

type ServicesPageContentProps = {
    searchParams?:
        | Promise<{ category?: string; service?: string }>
        | { category?: string; service?: string };
    showPageSchema?: boolean;
    showHero?: boolean;
};

export default async function ServicesPageContent({
    searchParams,
    showPageSchema = true,
    showHero = true,
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

    const locale = await getCurrentLocale();
    const cameraGuide = {
        ka: {
            title: "რამდენი კამერა სჭირდება კერძო სახლს?",
            description: "შეამოწმეთ, რა ზონები უნდა გააკონტროლოთ, როგორ დაგეგმოთ ხედვის კუთხეები და როგორ მოამზადოთ კამერების წინასწარი სქემა.",
            link: "წაიკითხეთ კამერების დაგეგმვის გზამკვლევი",
        },
        en: {
            title: "How many cameras does a house need?",
            description: "Identify the areas that need coverage, consider viewing angles and prepare an initial camera layout.",
            link: "Read the home camera planning guide",
        },
        ru: {
            title: "Сколько камер нужно для частного дома?",
            description: "Определите зоны контроля, оцените углы обзора и подготовьте предварительную схему размещения камер.",
            link: "Читать руководство по размещению камер",
        },
    }[locale];

    return (
        <div>
            {showPageSchema ? (
                <CmsPageSchema pageKey="services" fallback={<ServicesSchema />} />
            ) : null}
            {showHero ? <HeroSection /> : null}
            <PartnerSection />
            <ServiceSection
                category={category || undefined}
                initialService={selectedService || undefined}
            />
            <FeaturedSection selectedService={selectedService || undefined} />
            {showPageSchema && !category ? (
                <section className="mx-auto max-w-container-max px-5 py-10 md:px-8 xl:px-14">
                    <div className="rounded-3xl border border-primary/20 bg-primary/10 p-7 sm:p-10">
                        <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                            {cameraGuide.title}
                        </h2>
                        <p className="mt-4 max-w-3xl leading-8 text-on-surface-variant">
                            {cameraGuide.description}
                        </p>
                        <LocalizedLink
                            className="mt-6 inline-flex rounded-xl bg-primary-container px-6 py-4 font-semibold text-on-primary-container"
                            href="/guides/how-many-cameras-for-a-house"
                        >
                            {cameraGuide.link}
                        </LocalizedLink>
                    </div>
                </section>
            ) : null}
            <WhySection />
            <WorkSection />
            <FaqSeqAction />
            <CtaSection />
        </div>
    );
}
