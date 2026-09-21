import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";

import LocalServiceLinks from "@/components/seo/LocalServiceLinks";
import ServiceDetailView from "@/features/service-detail/ServiceDetailView";
import ServiceStructuredData from "@/features/service-detail/components/ServiceStructuredData";
import { getBackendService } from "@/lib/backend";
import { confirmBackendResourceNotFound } from "@/lib/backend-resource-status";
import { getLocalServiceLandings } from "@/lib/local-service-landings";
import { createMetadata, localizeHref, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type ServicePageProps = {
    params: Promise<{ slug: string }>;
};

function normalizedSeoTitle(value: string | null | undefined, siteName: string) {
    return (value || "")
        .replace(new RegExp(`\\s*[|—-]\\s*${siteName}\\s*$`, "i"), "")
        .replace(/\s+/g, " ")
        .trim()
        .toLocaleLowerCase();
}

function countryServiceTitle(serviceName: string, locale: string) {
    switch (locale) {
        case "en":
            return `${serviceName} in Georgia`;
        case "ru":
            return `${serviceName} в Грузии`;
        default:
            return `${serviceName} საქართველოში`;
    }
}

export async function generateMetadata({
    params,
}: ServicePageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding, locale, translations }, service, localLandings] =
        await Promise.all([
            getSiteSettings(),
            getBackendService(slug),
            getLocalServiceLandings(slug),
        ]);
    const siteName = branding.siteName;

    if (!service) {
        await confirmBackendResourceNotFound(
            `/services/${encodeURIComponent(slug)}`,
            { locale },
        );

        return {
            title: withSiteTitle(
                translateText(
                    translations,
                    "meta.service.notFound",
                    locale,
                    null,
                ),
                siteName,
            ),
            robots: { index: false, follow: false },
        };
    }

    const configuredTitle = service.seo?.title || service.title || service.name;
    const normalizedConfiguredTitle = normalizedSeoTitle(configuredTitle, siteName);
    const duplicatesLocalLanding = localLandings.some((landing) =>
        [landing.seo?.title, landing.title].some(
            (candidate) =>
                normalizedSeoTitle(candidate, siteName) === normalizedConfiguredTitle,
        ),
    );
    const metadataTitle = duplicatesLocalLanding
        ? countryServiceTitle(service.name || service.title || configuredTitle, locale)
        : configuredTitle;

    return createMetadata({
        title: metadataTitle,
        description:
            service.seo?.description ||
            service.seoDescription ||
            service.description,
        path: `/services/${service.slug}`,
        locale,
        keywords: service.seo?.keywords?.length
            ? service.seo.keywords
            : service.keywords,
        image:
            service.seo?.image ||
            service.heroImage ||
            branding.defaultImage ||
            undefined,
        siteName,
        noindex: Boolean(service.seo?.noindex),
        canonical: service.seo?.canonical,
        ogTitle: service.seo?.og?.title,
        ogDescription: service.seo?.og?.description,
    });
}

export default async function ServicePage({ params }: ServicePageProps) {
    const { slug } = await params;
    const [{ locale, socialSharing }, service] = await Promise.all([
        getSiteSettings(),
        getBackendService(slug),
    ]);

    if (!service) {
        await confirmBackendResourceNotFound(
            `/services/${encodeURIComponent(slug)}`,
            { locale },
        );
        notFound();
    }

    return (
        <>
            <ServiceStructuredData service={service} />
            <ServiceDetailView
                locale={locale}
                service={service}
                sharing={socialSharing}
            />
            {service.slug === "security-camera-installation" ? (
                <section className="mx-auto max-w-container-max px-5 py-12 md:px-8 xl:px-14">
                    <div className="rounded-3xl border border-primary/20 bg-primary/10 p-7 sm:p-10">
                        <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                            {{
                                ka: "დაგეგმეთ კამერების განლაგება მონტაჟამდე",
                                en: "Plan camera placement before installation",
                                ru: "Спланируйте размещение камер до монтажа",
                            }[locale]}
                        </h2>
                        <p className="mt-4 max-w-3xl leading-8 text-on-surface-variant">
                            {{
                                ka: "ატვირთეთ ობიექტის გეგმა, მონიშნეთ კამერები და ბრმა ზონები და გამოგვიგზავნეთ წინასწარი პროექტი. საბოლოო ხედვა და მოწყობილობები ადგილზე მოწმდება.",
                                en: "Upload a property plan, mark cameras and potential blind spots, then send an initial design. Final coverage and equipment are verified on site.",
                                ru: "Загрузите план объекта, отметьте камеры и возможные слепые зоны и отправьте предварительный проект. Итоговое покрытие и оборудование проверяются на объекте.",
                            }[locale]}
                        </p>
                        <Link
                            className="mt-7 inline-flex rounded-xl bg-primary-container px-6 py-4 font-semibold text-on-primary-container"
                            href={localizeHref("/camera-planner", locale)}
                        >
                            {{
                                ka: "გახსენით კამერების პლანერი",
                                en: "Open camera placement planner",
                                ru: "Открыть планировщик камер",
                            }[locale]}
                        </Link>
                    </div>
                </section>
            ) : null}
            <LocalServiceLinks locale={locale} serviceSlug={service.slug} />
        </>
    );
}
