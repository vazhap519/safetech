import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroTypography() {
    const { locale, translations } = await getSiteSettings();
    const description = translateText(
        translations,
        "services.hero.description",
        locale,
        null,
    );

    if (!description) return null;

    return (
        <Typography as="p" className="mb-unit-lg max-w-lg" variant="description">
            {description}
        </Typography>
    );
}
