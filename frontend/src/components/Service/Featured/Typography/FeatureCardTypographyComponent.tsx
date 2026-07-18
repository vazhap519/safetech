import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FeatureCardTypographyComponent() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "services.featured.title",
        locale,
        null,
    );

    if (!title) return null;

    return (
        <Typography
            as="h2"
            className="mb-unit-xl text-center font-headline-lg text-headline-lg"
            variant="section-title"
        >
            {title}
        </Typography>
    );
}
