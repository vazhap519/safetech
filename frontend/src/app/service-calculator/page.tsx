import ServiceCalculator from "@/components/calculator/ServiceCalculator";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import JsonLd from "@/components/seo/JsonLd";
import { getBackendCalculatorProfiles } from "@/lib/backend";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { getLanguageTag } from "@/lib/locales";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import { absoluteLocalizedUrl } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.serviceCalculator);
}

type CalculatorPageProps = {
    searchParams?: Promise<{ service?: string }>;
};

export default async function CalculatorPage({ searchParams }: CalculatorPageProps) {
    const [{ locale }, query] = await Promise.all([
        getSiteSettings(),
        searchParams ?? Promise.resolve<{ service?: string }>({}),
    ]);
    const profiles = await getBackendCalculatorProfiles(locale);
    const title = locale === "en"
        ? "IT and security service calculator"
        : locale === "ru"
          ? "Калькулятор IT-услуг и систем безопасности"
          : "IT და უსაფრთხოების სერვისების კალკულატორი";
    const description = locale === "en"
        ? "Indicative project budget based on your site and service requirements."
        : locale === "ru"
          ? "Ориентировочный бюджет проекта с учетом параметров объекта и услуги."
          : "პროექტის საორიენტაციო ბიუჯეტი ობიექტისა და არჩეული სერვისის პარამეტრების მიხედვით.";
    const schema = {
        "@context": "https://schema.org",
        "@type": "WebPage",
        name: title,
        description,
        url: absoluteLocalizedUrl("/service-calculator", locale),
        inLanguage: getLanguageTag(locale),
        mainEntity: {
            "@type": "ItemList",
            itemListElement: profiles.map((profile, index) => ({
                "@type": "ListItem",
                position: index + 1,
                name: profile.name,
            })),
        },
    };

    return (
        <>
            <CmsPageSchema
                pageKey="service-calculator"
                fallback={<JsonLd data={schema} />}
            />
            <ServiceCalculator
                initialService={query.service}
                profiles={profiles}
            />
        </>
    );
}
