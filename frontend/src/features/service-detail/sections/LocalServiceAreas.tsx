import LocalizedLink from "@/components/ui/LocalizedLink";
import { getLocalServiceLandings } from "@/lib/local-service-landings";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function LocalServiceAreas({
    serviceSlug,
}: {
    serviceSlug: string;
}) {
    const [{ locale, translations }, landings] = await Promise.all([
        getSiteSettings(),
        getLocalServiceLandings(serviceSlug),
    ]);

    if (!landings.length) return null;

    const title = translateText(
        translations,
        "service.localAreas.title",
        locale,
        {
            ka: "მომსახურების ზონები",
            en: "Service areas",
            ru: "Зоны обслуживания",
        },
    );
    const description = translateText(
        translations,
        "service.localAreas.description",
        locale,
        {
            ka: "იხილეთ კონკრეტული ქალაქისთვის განკუთვნილი მომსახურება, რეალური პროექტები და ადგილობრივი სამუშაოს დეტალები.",
            en: "Explore location-specific service details, real projects, and local delivery information.",
            ru: "Посмотрите услуги по конкретным городам, реальные проекты и детали выездных работ.",
        },
    );

    return (
        <section className="border-y border-white/10 bg-surface-container-low/35 px-5 py-14 md:px-8 xl:px-14">
            <div className="mx-auto max-w-container-max">
                <div className="max-w-3xl">
                    <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                        {title}
                    </h2>
                    <p className="mt-3 leading-7 text-on-surface-variant">
                        {description}
                    </p>
                </div>
                <div className="mt-8 flex flex-wrap gap-3">
                    {landings.map((landing) => (
                        <LocalizedLink
                            className="rounded-xl border border-primary/20 bg-primary/10 px-5 py-3 font-medium text-primary transition hover:border-primary/50 hover:bg-primary/15"
                            href={`/services/${serviceSlug}/${landing.locationSlug}`}
                            key={landing.id}
                        >
                            {landing.locationName}
                        </LocalizedLink>
                    ))}
                </div>
            </div>
        </section>
    );
}
