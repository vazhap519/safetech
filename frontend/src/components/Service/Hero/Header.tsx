import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Header() {
    const { locale, translations } = await getSiteSettings();
    const titlePrefix = translateText(
        translations,
        "services.hero.titlePrefix",
        locale,
        null,
    );
    const titleAccent = translateText(
        translations,
        "services.hero.titleAccent",
        locale,
        null,
    );
    const titleSuffix = translateText(
        translations,
        "services.hero.titleSuffix",
        locale,
        null,
    );

    if (!titlePrefix && !titleAccent && !titleSuffix) {
        return null;
    }

    return (
        <Typography as="h1" variant="[slug]-hero">
            {titlePrefix}
            {titlePrefix && (titleAccent || titleSuffix) ? (
                <>
                    <br />
                    {" "}
                </>
            ) : null}
            {titleAccent ? (
                <>
                    <span className="text-primary">{titleAccent}</span>{" "}
                </>
            ) : null}
            {titleSuffix}
        </Typography>
    );
}
