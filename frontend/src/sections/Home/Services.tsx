import Servicecomponent from "@/components/Home/Servicecomponent";
import Typography from "@/components/ui/Typography";
import { getBackendServices } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Services() {
    const [services, { locale, translations }] = await Promise.all([
        getBackendServices(),
        getSiteSettings(),
    ]);

    if (!services.length) return null;

    const eyebrow = translateText(
        translations,
        "home.services.eyebrow",
        locale,
        null,
    );
    const title = translateText(translations, "home.services.title", locale, null);
    const description = translateText(
        translations,
        "home.services.description",
        locale,
        null,
    );

    return (
        <section className="relative mx-auto max-w-container-max overflow-hidden px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14">
            {eyebrow || title || description ? (
                <div className="mb-10 flex flex-col items-center text-center md:mb-14">
                    {eyebrow ? (
                        <span className="mb-3 font-mono-sm text-mono-sm font-semibold uppercase text-primary">
                            {eyebrow}
                        </span>
                    ) : null}

                    {title ? (
                        <Typography as="h2" className="max-w-4xl" variant="section-title">
                            {title}
                        </Typography>
                    ) : null}

                    {description ? (
                        <p className="mt-4 max-w-2xl text-base leading-[1.7] text-on-surface-variant md:text-[17px]">
                            {description}
                        </p>
                    ) : null}
                </div>
            ) : null}

            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                {services.slice(0, 4).map((service) => (
                    <Servicecomponent key={service.slug} service={service} />
                ))}
            </div>

        </section>
    );
}
