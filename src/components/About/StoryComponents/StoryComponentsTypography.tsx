import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function StoryComponentsTypography() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <div className="space-y-unit-lg">
            <Typography as="h2" variant="section-title">
                {t("about.story.title", {
                    ka: "ინჟინერია და ხარისხი",
                    en: "Engineering and quality",
                    ru: "Инженерия и качество",
                })}
            </Typography>

            <div className="h-1 w-20 bg-primary" />

            <Typography as="p" variant="section-description">
                {t("about.story.paragraph.0", {
                    ka: "ჩვენი ისტორია დაიწყო რწმენით, რომ ნებისმიერი ციფრული ტრანსფორმაციის საფუძველი არის მყარი და უსაფრთხო ფიზიკური ინფრასტრუქტურა. SafeTech აერთიანებს მაღალი დონის საინჟინრო ექსპერტიზასა და უახლეს ტექნოლოგიებს.",
                    en: "Our story began with a simple belief: every digital transformation depends on strong and secure physical infrastructure. SafeTech combines advanced engineering expertise with modern technology.",
                    ru: "Наша история началась с убеждения, что в основе любой цифровой трансформации лежит надежная и безопасная физическая инфраструктура. SafeTech объединяет инженерную экспертизу высокого уровня и современные технологии.",
                })}
            </Typography>

            <Typography as="p" variant="section-description">
                {t("about.story.paragraph.1", {
                    ka: "ჩვენი მიდგომა ეფუძნება სისუფთავეს, სიზუსტეს და გრძელვადიან საიმედოობას. თითოეული კაბელი, თითოეული სენსორი და თითოეული სერვერი არის კარგად გააზრებული არქიტექტურის ნაწილი.",
                    en: "Our approach is built on precision, discipline, and long-term reliability. Every cable, sensor, and server is part of a carefully planned architecture.",
                    ru: "Наш подход основан на точности, дисциплине и долгосрочной надежности. Каждый кабель, датчик и сервер являются частью продуманной архитектуры.",
                })}
            </Typography>
        </div>
    );
}
