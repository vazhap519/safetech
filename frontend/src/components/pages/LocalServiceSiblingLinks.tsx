import LocalizedLink from "@/components/ui/LocalizedLink";
import type { LocalServiceLanding } from "@/lib/local-service-landings";
import type { Locale } from "@/lib/locales";

const labels: Record<Locale, { title: string; text: string }> = {
    ka: {
        title: "მომსახურება სხვა ქალაქებში",
        text: "ნახეთ იგივე სერვისი SafeTech-ის სხვა მომსახურების ზონებში.",
    },
    en: {
        title: "Service in other locations",
        text: "Explore the same SafeTech service in other service areas.",
    },
    ru: {
        title: "Услуга в других городах",
        text: "Посмотрите эту же услугу SafeTech в других зонах обслуживания.",
    },
};

export default function LocalServiceSiblingLinks({
    currentLocation,
    serviceSlug,
    siblings,
    locale,
}: {
    currentLocation: string;
    serviceSlug: string;
    siblings: LocalServiceLanding[];
    locale: Locale;
}) {
    const links = siblings.filter(
        (landing) => landing.locationSlug !== currentLocation && landing.seo?.noindex !== true,
    );

    if (!links.length) return null;

    const copy = labels[locale];

    return (
        <section className="border-t border-white/10 bg-surface-container-low/35 px-5 py-14 md:px-8 xl:px-14">
            <div className="mx-auto max-w-container-max">
                <div className="max-w-3xl">
                    <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                        {copy.title}
                    </h2>
                    <p className="mt-3 leading-7 text-on-surface-variant">{copy.text}</p>
                </div>
                <div className="mt-7 flex flex-wrap gap-3">
                    {links.map((landing) => (
                        <LocalizedLink
                            className="rounded-full border border-primary/20 bg-primary/10 px-4 py-2 font-medium text-primary transition hover:border-primary/50 hover:bg-primary/15"
                            href={`/services/${serviceSlug}/${landing.locationSlug}`}
                            key={landing.locationSlug}
                        >
                            {landing.title}
                        </LocalizedLink>
                    ))}
                </div>
            </div>
        </section>
    );
}
