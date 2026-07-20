import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroTypography() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "about.hero.title", locale, {
        ka: "SafeTech-ის შესახებ",
        en: "About SafeTech",
        ru: "О SafeTech",
    });
    const description = translateText(
        translations,
        "about.hero.description",
        locale,
        {
            ka: "ვქმნით და ვმართავთ ბიზნესისთვის საჭირო IT ინფრასტრუქტურასა და უსაფრთხოების სისტემებს.",
            en: "We design and manage the IT infrastructure and security systems businesses need.",
            ru: "Мы проектируем и обслуживаем IT-инфраструктуру и системы безопасности для бизнеса.",
        },
    );

    if (!title && !description) return null;

    return (
        <>
            {title ? (
                <Typography
                    as="h1"
                    className="mx-auto mb-unit-md max-w-4xl text-center"
                    variant="hero"
                >
                    {title}
                </Typography>
            ) : null}
            {description ? (
                <Typography as="p" variant="section-description">
                    {description}
                </Typography>
            ) : null}
        </>
    );
}
