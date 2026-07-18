import WhyComponent from "@/components/Home/Whycomponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const whyItems = [
    { icon: "verified", key: "home.why.items.0" },
    { icon: "security", key: "home.why.items.1" },
    { icon: "hub", key: "home.why.items.2" },
    { icon: "support_agent", key: "home.why.items.3" },
    { icon: "schema", key: "home.why.items.4" },
    { icon: "monitoring", key: "home.why.items.5" },
];

export default async function Why() {
    const { locale, translations } = await getSiteSettings();
    const eyebrow = translateText(translations, "home.why.eyebrow", locale, null);
    const title = translateText(translations, "home.why.title", locale, null);
    const description = translateText(
        translations,
        "home.why.description",
        locale,
        null,
    );
    const items = whyItems
        .map((item) => ({
            ...item,
            description: translateText(
                translations,
                `${item.key}.description`,
                locale,
                null,
            ),
            title: translateText(translations, `${item.key}.title`, locale, null),
        }))
        .filter((item) => item.title || item.description);

    if (!eyebrow && !title && !description && !items.length) return null;

    return (
        <section className="relative overflow-hidden bg-surface-container-low py-20 md:py-28">
            <div className="mx-auto max-w-container-max px-5 md:px-10 xl:px-margin-desktop">
                {eyebrow || title || description ? (
                    <div className="mb-14 text-center md:mb-20">
                        {eyebrow ? (
                            <span className="mb-4 inline-block font-mono-sm text-mono-sm uppercase tracking-[0.3em] text-primary">
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
                    <div className="grid grid-cols-1 gap-6 md:gap-gutter sm:grid-cols-2 xl:grid-cols-3">
                        {items.map((item) => (
                            <WhyComponent
                                description={item.description}
                                icon={item.icon}
                                key={item.key}
                                title={item.title}
                            />
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
