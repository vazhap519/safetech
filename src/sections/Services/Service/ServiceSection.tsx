import ServiceCardComponent from "@/components/Service/ServiceCard/ServiceCardComponent";
import ServiceTypographyComponent from "@/components/Service/ServiceTypography/ServiceTypographyComponent";
import { getBackendServices } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ServiceSection() {
    const [services, { locale, translations }] = await Promise.all([
        getBackendServices(),
        getSiteSettings(),
    ]);

    const emptyStateTitle = translateText(
        translations,
        "services.catalog.empty.title",
        locale,
        {
            ka: "სერვისები ჯერ არ არის დამატებული",
            en: "Services have not been added yet",
            ru: "Услуги пока не добавлены",
        },
    );
    const emptyStateDescription = translateText(
        translations,
        "services.catalog.empty.description",
        locale,
        {
            ka: "ადმინისტრატორის მხრიდან კონტენტის დამატების შემდეგ სერვისები აქ ავტომატურად გამოჩნდება.",
            en: "Services will appear here automatically after they are added from the admin panel.",
            ru: "Услуги автоматически появятся здесь после добавления из админ-панели.",
        },
    );

    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl">
            <ServiceTypographyComponent />

            <div className="mb-unit-xl flex flex-col gap-2 text-sm text-on-surface-variant sm:flex-row sm:items-center sm:justify-between">
                <p>
                    {services.length}{" "}
                    {translateText(translations, "services.catalog.count", locale, {
                        ka: "აქტიური სერვისი",
                        en: "active services",
                        ru: "активных услуг",
                    })}
                </p>
                <p>
                    {translateText(
                        translations,
                        "services.catalog.helper",
                        locale,
                        {
                            ka: "ყველა სერვისი ხელმისაწვდომია უშუალოდ ამ გვერდიდან.",
                            en: "Every service is available directly from this page.",
                            ru: "Все услуги доступны напрямую с этой страницы.",
                        },
                    )}
                </p>
            </div>

            {services.length ? (
                <div className="mb-unit-xl grid gap-gutter md:grid-cols-2 lg:grid-cols-3">
                    {services.map((service) => (
                        <ServiceCardComponent key={service.slug} service={service} />
                    ))}
                </div>
            ) : (
                <div className="glass-card mb-unit-xl rounded-3xl border border-dashed border-outline-variant/30 px-unit-lg py-unit-xl text-center">
                    <h3 className="font-headline-md text-headline-md text-white">
                        {emptyStateTitle}
                    </h3>
                    <p className="mx-auto mt-3 max-w-2xl text-on-surface-variant">
                        {emptyStateDescription}
                    </p>
                </div>
            )}
        </section>
    );
}
