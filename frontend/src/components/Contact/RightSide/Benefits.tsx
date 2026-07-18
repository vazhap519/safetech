import Icon from "@/components/ui/Icon";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Benefits() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "contact.side.title", locale, null);
    const description = translateText(
        translations,
        "contact.side.description",
        locale,
        null,
    );

    if (!title && !description) return null;

    return (
        <div className="glass-panel rounded-xl p-unit-md transition-transform duration-300 hover:scale-105 lg:col-span-5">
            <div className="flex items-start gap-unit-md">
                <Icon className="text-4xl text-secondary" name="architecture" />
                <div>
                    {title ? (
                        <Typography as="h3" variant="contact-title">
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
            </div>
        </div>
    );
}
