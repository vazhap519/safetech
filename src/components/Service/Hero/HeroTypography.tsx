import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroTypography() {
    const { locale, translations } = await getSiteSettings();

    return (
        <Typography
            as="p"
            variant="description"
            className="
                mb-unit-lg
                max-w-lg
            "
        >
            {translateText(
                translations,
                "services.hero.description",
                locale,
                {
                    ka: "ჩვენ ვუზრუნველყოფთ თქვენი ბიზნესის ციფრულ და ფიზიკურ უსაფრთხოებას უახლესი ტექნოლოგიებითა და ექსპერტული ცოდნით.",
                    en: "We secure your business with modern digital and physical infrastructure backed by expert implementation.",
                    ru: "Мы обеспечиваем цифровую и физическую безопасность вашего бизнеса с помощью современных технологий и экспертной реализации.",
                },
            )}
        </Typography>
    );
}
