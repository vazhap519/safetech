import LocalizedLink from "@/components/ui/LocalizedLink";
import { getLocalServiceLandings } from "@/lib/local-service-landings";
import type { Locale } from "@/lib/locales";

const labels: Record<Locale, { title: string; text: string; cta: string }> = {
    ka: {
        title: "მომსახურება ქალაქების მიხედვით",
        text: "აირჩიეთ თქვენი ქალაქი და ნახეთ კონკრეტული მომსახურების პირობები, ადგილობრივი გამოცდილება და შესაბამისი პროექტები.",
        cta: "ნახე მომსახურება",
    },
    en: {
        title: "Service by location",
        text: "Choose your city to see local service details, relevant experience, and completed projects.",
        cta: "View local service",
    },
    ru: {
        title: "Услуга по городам",
        text: "Выберите город, чтобы посмотреть локальные условия, опыт и соответствующие реализованные проекты.",
        cta: "Посмотреть услугу",
    },
};

export default async function LocalServiceLinks({
    serviceSlug,
    locale,
}: {
    serviceSlug: string;
    locale: Locale;
}) {
    const landings = (await getLocalServiceLandings(serviceSlug)).filter(
        (landing) => !landing.seo?.noindex,
    );

    if (!landings.length) return null;

    const copy = labels[locale];

    return (
        <section
            aria-labelledby="local-service-links-title"
            className="border-t border-white/10 bg-surface-container-low/45 px-5 py-14 md:px-8 xl:px-14"
        >
            <div className="mx-auto max-w-container-max">
                <div className="max-w-3xl">
                    <h2
                        className="text-2xl font-semibold text-on-surface md:text-3xl"
                        id="local-service-links-title"
                    >
                        {copy.title}
                    </h2>
                    <p className="mt-3 leading-7 text-on-surface-variant">
                        {copy.text}
                    </p>
                </div>

                <ul className="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    {landings.map((landing) => (
                        <li key={landing.id}>
                            <LocalizedLink
                                className="group flex h-full items-center justify-between gap-4 rounded-2xl border border-white/10 bg-surface-container p-5 transition hover:-translate-y-0.5 hover:border-primary/40"
                                href={`/services/${landing.service.slug}/${landing.locationSlug}`}
                                prefetch={false}
                            >
                                <span>
                                    <span className="block font-semibold text-on-surface group-hover:text-primary">
                                        {landing.locationName}
                                    </span>
                                    <span className="mt-1 block text-sm text-on-surface-variant">
                                        {copy.cta}
                                    </span>
                                </span>
                                <span aria-hidden="true" className="text-xl text-primary">
                                    →
                                </span>
                            </LocalizedLink>
                        </li>
                    ))}
                </ul>
            </div>
        </section>
    );
}
