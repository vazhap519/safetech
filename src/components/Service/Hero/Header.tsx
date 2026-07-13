import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Header() {
    const { locale, translations } = await getSiteSettings();

    return (
        <Typography as="h1" variant="[slug]-hero">
            {translateText(translations, "services.hero.titlePrefix", locale, {
                ka: "პროფესიონალური",
                en: "Professional",
                ru: "Профессиональные",
            })}{" "}
            <br />
            <span className="text-primary">
                {translateText(translations, "services.hero.titleAccent", locale, {
                    ka: "IT და უსაფრთხოების",
                    en: "IT and security",
                    ru: "IT- и охранные",
                })}
            </span>{" "}
            {translateText(translations, "services.hero.titleSuffix", locale, {
                ka: "სერვისები",
                en: "services",
                ru: "услуги",
            })}
        </Typography>
    );
}
