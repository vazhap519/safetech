import Industriescomponent from "@/components/Home/Industriescomponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const industries = [
    { icon: "apartment", key: "home.industries.items.0" },
    { icon: "storefront", key: "home.industries.items.1" },
    { icon: "warehouse", key: "home.industries.items.2" },
    { icon: "manufacturing", key: "home.industries.items.3" },
];

export default async function Industries() {
    const { locale, translations } = await getSiteSettings();
    const eyebrow = translateText(
        translations,
        "home.industries.eyebrow",
        locale,
        null,
    );
    const title = translateText(translations, "home.industries.title", locale, null);
    const description = translateText(
        translations,
        "home.industries.description",
        locale,
        null,
    );
    const items = industries
        .map((item) => ({
            ...item,
            title: translateText(translations, item.key, locale, null),
        }))
        .filter((item) => item.title);

    if (!eyebrow && !title && !description && !items.length) return null;

    return (
        <section className="relative mx-auto max-w-container-max overflow-hidden px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14">
            {eyebrow || title || description ? (
                <div className="mb-10 text-center md:mb-14">
                    {eyebrow ? (
                        <span className="mb-3 inline-block font-mono-sm text-mono-sm font-semibold uppercase text-primary">
                            {eyebrow}
                        </span>
                    ) : null}
                    {title ? (
                        <Typography as="h2" variant="section-title">
                            {title}
                        </Typography>
                    ) : null}
                    {description ? (
                        <Typography
                            as="p"
                            className="mx-auto max-w-2xl"
                            variant="section-description"
                        >
                            {description}
                        </Typography>
                    ) : null}
                </div>
            ) : null}

            {items.length ? (
                <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
                    {items.map((item) => (
                        <Industriescomponent
                            icon={item.icon}
                            key={item.key}
                            title={item.title}
                        />
                    ))}
                </div>
            ) : null}
        </section>
    );
}
