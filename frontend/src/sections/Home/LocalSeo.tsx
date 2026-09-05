import ActionLink from "@/components/ui/ActionLink";
import Typography from "@/components/ui/Typography";
import { getLocalServiceLandings } from "@/lib/local-service-landings";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function LocalSeo() {
    const [{ locale, translations }, landings] = await Promise.all([
        getSiteSettings(),
        getLocalServiceLandings(),
    ]);

    if (!landings.length) return null;

    const eyebrow = translateText(translations, "home.localSeo.eyebrow", locale, {
        ka: "მომსახურება ქალაქების მიხედვით",
        en: "Services by location",
        ru: "Услуги по городам",
    });
    const title = translateText(translations, "home.localSeo.title", locale, {
        ka: "SafeTech თქვენს ქალაქშიც მუშაობს",
        en: "SafeTech works in your city too",
        ru: "SafeTech работает и в вашем городе",
    });
    const description = translateText(translations, "home.localSeo.description", locale, {
        ka: "იხილეთ კონკრეტული სერვისები და რეალური შეთავაზებები თქვენი ქალაქისთვის. ადგილზე ვემსახურებით თბილისსა და საქართველოს რეგიონებს შეთანხმებით.",
        en: "Explore services and tailored solutions available in your city. We provide on-site service in Tbilisi and across Georgia by arrangement.",
        ru: "Посмотрите услуги и решения для вашего города. Выезжаем по Тбилиси и регионам Грузии по договоренности.",
    });

    const unique = Array.from(
        new Map(
            landings.map((landing) => [
                `${landing.service.slug}:${landing.locationSlug}`,
                landing,
            ]),
        ).values(),
    ).slice(0, 12);

    return (
        <section className="mx-auto max-w-container-max px-5 py-14 sm:px-6 lg:px-14 lg:py-20">
            <div className="rounded-3xl border border-outline-variant/30 bg-surface-container/40 p-6 sm:p-8 lg:p-10">
                <div className="max-w-3xl">
                    <p className="text-sm font-semibold uppercase tracking-[0.14em] text-primary">
                        {eyebrow}
                    </p>
                    <Typography as="h2" className="mt-3" variant="section-title">
                        {title}
                    </Typography>
                    <Typography as="p" className="mt-4 max-w-2xl" variant="description">
                        {description}
                    </Typography>
                </div>

                <div className="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    {unique.map((landing) => (
                        <ActionLink
                            key={`${landing.service.slug}-${landing.locationSlug}`}
                            className="min-h-12 justify-start px-4 py-3 text-left"
                            href={`/services/${landing.service.slug}/${landing.locationSlug}`}
                            variant="glass"
                        >
                            {landing.service.name} — {landing.locationName}
                        </ActionLink>
                    ))}
                </div>
            </div>
        </section>
    );
}
