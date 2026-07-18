import WhyComponent from "@/components/About/WhyComponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const itemIcons = ["architecture", "verified", "support_agent", "hub"];

export default async function WhySection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "about.why.title", locale, null);
    const description = translateText(
        translations,
        "about.why.description",
        locale,
        null,
    );
    const items = itemIcons
        .map((icon, index) => ({
            icon,
            title: translateText(
                translations,
                `about.why.item.${index}.title`,
                locale,
                null,
            ),
            description: translateText(
                translations,
                `about.why.item.${index}.description`,
                locale,
                null,
            ),
        }))
        .filter((item) => item.title || item.description);

    if (!title && !description && !items.length) return null;

    return (
        <section className="py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title || description ? (
                    <div className="mb-unit-xl text-center">
                        {title ? (
                            <Typography
                                as="h2"
                                className="mb-unit-sm font-headline-lg text-headline-lg text-on-surface"
                                variant="section-title"
                            >
                                {title}
                            </Typography>
                        ) : null}
                        {description ? (
                            <Typography
                                as="p"
                                className="font-body-md text-body-md text-on-surface-variant"
                                variant="section-description"
                            >
                                {description}
                            </Typography>
                        ) : null}
                    </div>
                ) : null}

                {items.length ? (
                    <div className="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-4">
                        {items.map((item) => (
                            <WhyComponent key={`${item.icon}-${item.title}`} {...item} />
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
