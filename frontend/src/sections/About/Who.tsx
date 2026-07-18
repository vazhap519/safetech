import WhoComponent from "@/components/About/WhoComponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const itemIcons = ["security", "lan", "dns"];

export default async function WhoSection() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "about.who.title", locale, null);
    const description = translateText(
        translations,
        "about.who.description",
        locale,
        null,
    );
    const items = itemIcons
        .map((icon, index) => ({
            icon,
            title: translateText(
                translations,
                `about.who.item.${index}.title`,
                locale,
                null,
            ),
            description: translateText(
                translations,
                `about.who.item.${index}.description`,
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
                            <Typography as="h2" variant="section-title">
                                {title}
                            </Typography>
                        ) : null}
                        {description ? (
                            <Typography as="p" variant="section-description">
                                {description}
                            </Typography>
                        ) : null}
                    </div>
                ) : null}
                {items.length ? (
                    <div className="grid grid-cols-1 gap-gutter md:grid-cols-3">
                        {items.map((item) => (
                            <WhoComponent key={`${item.icon}-${item.title}`} {...item} />
                        ))}
                    </div>
                ) : null}
            </div>
        </section>
    );
}
