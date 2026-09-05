import LocalizedLink from "@/components/ui/LocalizedLink";
import type { LocalServiceLanding } from "@/lib/local-service-landings";
import type { Locale } from "@/lib/locales";

const labels: Record<Locale, { title: string; text: string }> = {
    ka: {
        title: "დაკავშირებული სერვისები და ლოკაციები",
        text: "იხილეთ ამ პროექტთან დაკავშირებული მომსახურება შესაბამის ქალაქში.",
    },
    en: {
        title: "Related services and locations",
        text: "Explore the service and location pages connected to this project.",
    },
    ru: {
        title: "Связанные услуги и города",
        text: "Посмотрите страницы услуг и городов, связанных с этим проектом.",
    },
};

export default function ProjectLocalSeoLinks({
    landings,
    locale,
}: {
    landings: LocalServiceLanding[];
    locale: Locale;
}) {
    if (!landings.length) return null;

    const copy = labels[locale];

    return (
        <section className="px-5 py-12 md:px-8 xl:px-14">
            <div className="mx-auto max-w-container-max rounded-3xl border border-white/10 bg-surface-container-low/55 p-6 md:p-8">
                <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                    {copy.title}
                </h2>
                <p className="mt-3 max-w-3xl leading-7 text-on-surface-variant">
                    {copy.text}
                </p>
                <div className="mt-6 flex flex-wrap gap-3">
                    {landings.map((landing) => (
                        <LocalizedLink
                            className="rounded-full border border-primary/30 bg-primary/10 px-4 py-2 text-sm font-medium text-primary transition hover:bg-primary/20"
                            href={`/services/${landing.service.slug}/${landing.locationSlug}`}
                            key={`${landing.service.slug}-${landing.locationSlug}`}
                        >
                            {landing.service.name} — {landing.locationName}
                        </LocalizedLink>
                    ))}
                </div>
            </div>
        </section>
    );
}
