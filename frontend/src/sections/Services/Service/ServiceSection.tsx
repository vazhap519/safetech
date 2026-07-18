import ServiceTypographyComponent from "@/components/Service/ServiceTypography/ServiceTypographyComponent";
import ContentFilterGrid from "@/components/filters/ContentFilterGrid";
import {
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
    const [services, categories, { locale, translations }] = await Promise.all([
        getBackendServices(category),
        getBackendFilterCategories("services"),
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

            <ContentFilterGrid
                activeCategory={category}
                categories={categories}
                items={services}
                kind="services"
            />
        </section>
    );
}
