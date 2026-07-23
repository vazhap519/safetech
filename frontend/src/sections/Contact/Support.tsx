import Items from "@/components/Contact/support/Items";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText, translateText } from "@/lib/translations";

const supportKeys = [
    "contact.support.title",
    "contact.support.description",
    "contact.support.badge",
    "contact.support.item.0",
    "contact.support.item.1",
    "contact.support.item.2",
    "contact.support.item.3",
    "contact.support.item.4",
    "contact.support.item.5",
];

export default async function Support() {
    const { branding, locale, translations } = await getSiteSettings();

    if (!hasTranslatedText(translations, supportKeys, locale)) {
        return null;
    }

    const title = translateText(translations, "contact.support.title", locale, null);
    const description = translateText(
        translations,
        "contact.support.description",
        locale,
        null,
    );
    const badge = translateText(translations, "contact.support.badge", locale, null);
    const imageAlt = translateText(
        translations,
        "contact.support.imageAlt",
        locale,
        null,
    );

    return (
        <section className="relative overflow-hidden py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 items-center gap-unit-lg px-margin-desktop lg:grid-cols-2 lg:gap-unit-xl">
                {branding.defaultImage ? (
                    <div className="relative order-1">
                        <Image
                            alt={imageAlt || title}
                            sizes="(max-width: 768px) 100vw, 50vw"
                            src={branding.defaultImage}
                            variant="contact-support"
                        />

                        {badge ? (
                            <div className="glass-panel absolute bottom-4 right-4 rounded-xl border-secondary/30 p-3 md:bottom-6 md:right-6 md:p-4">
                                <div className="flex items-center gap-2">
                                    <div className="h-2 w-2 animate-pulse rounded-full bg-secondary md:h-3 md:w-3" />
                                    <span className="whitespace-nowrap text-xs uppercase tracking-wider md:text-sm">
                                        {badge}
                                    </span>
                                </div>
                            </div>
                        ) : null}
                    </div>
                ) : null}

                <div className="order-2">
                    {title ? (
                        <Typography as="h2" variant="contact-support-title">
                            {title}
                        </Typography>
                    ) : null}

                    {description ? (
                        <Typography
                            as="p"
                            className="mb-unit-md"
                            variant="description"
                        >
                            {description}
                        </Typography>
                    ) : null}

                    <ul className="space-y-unit-sm">
                        <Items />
                    </ul>
                </div>
            </div>
        </section>
    );
}
