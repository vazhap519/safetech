import ServiceCalculator from "@/components/calculator/ServiceCalculator";
import ServiceTypographyComponent from "@/components/Service/ServiceTypography/ServiceTypographyComponent";
import ContentFilterGrid from "@/components/filters/ContentFilterGrid";
import {
    getBackendCalculatorProfiles,
    getBackendFilterCategories,
    getBackendServices,
} from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServiceSection({
    category,
}: {
    category?: string;
}) {
    const [services, categories, profiles, { locale, translations }] = await Promise.all([
        getBackendServices(category),
        getBackendFilterCategories("services"),
        getBackendCalculatorProfiles(),
        getSiteSettings(),
    ]);

    if (!services.length) return null;

    const countLabel = translateText(
        translations,
        "services.catalog.count",
        locale,
        null,
    );
    const helperText = translateText(
        translations,
        "services.catalog.helper",
        locale,
        null,
    );

    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl">
            <ServiceTypographyComponent />

            {countLabel || helperText ? (
                <div className="mb-unit-xl flex flex-col gap-2 text-sm text-on-surface-variant sm:flex-row sm:items-center sm:justify-between">
                    {countLabel ? (
                        <p>
                            {services.length} {countLabel}
                        </p>
                    ) : null}
                    {helperText ? <p>{helperText}</p> : null}
                </div>
            ) : null}

            <div className={profiles.length ? "grid gap-8 xl:grid-cols-[minmax(0,1fr)_26rem] xl:items-start" : ""}>
                <div>
                    <ContentFilterGrid
                        activeCategory={category}
                        categories={categories}
                        items={services}
                        kind="services"
                    />
                </div>

                {profiles.length ? (
                    <div className="xl:sticky xl:top-28">
                        <ServiceCalculator
                            initialService={services[0]?.slug}
                            profiles={profiles}
                            variant="embedded"
                        />
                    </div>
                ) : null}
            </div>
        </section>
    );
}
