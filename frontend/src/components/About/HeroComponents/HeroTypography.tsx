import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function HeroTypography() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <>
            <Typography
                as="h1"
                variant="hero"
                className="mx-auto mb-unit-md max-w-4xl text-center"
            >
                {t("about.hero.title", {
                    ka: "ვქმნით უსაფრთხო და საიმედო ინფრასტრუქტურას",
                    en: "We build secure and reliable infrastructure",
                    ru: "Мы создаем безопасную и надежную инфраструктуру",
                })}
            </Typography>
            <Typography as="p" variant="section-description">
                {t("about.hero.description", {
                    ka: "SafeTech ეხმარება ბიზნესებს უსაფრთხოების, ქსელური და IT ინფრასტრუქტურის თანამედროვე სისტემების შექმნაში.",
                    en: "SafeTech helps businesses build modern security, networking, and IT infrastructure systems.",
                    ru: "SafeTech помогает бизнесу создавать современные системы безопасности, сетевую и IT-инфраструктуру.",
                })}
            </Typography>
        </>
    );
}
