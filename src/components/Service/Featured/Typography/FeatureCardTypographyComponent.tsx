import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FeatureCardTypographyComponent () {
    const { locale, translations } = await getSiteSettings();

    return (
        <Typography as={"h2"} variant={"section-title"} className={"font-headline-lg text-headline-lg text-center mb-unit-xl"}>
            {translateText(translations, "services.featured.title", locale, {
                ka: "ყველაზე მოთხოვნადი სერვისები",
                en: "Most requested services",
                ru: "Самые востребованные услуги",
            })}
        </Typography>

    )
}
