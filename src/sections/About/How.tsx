import HowComponent from "@/components/About/HowComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function HowSection() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const steps = [
        {
            title: t("about.how.item.0.title", {
                ka: "კონსულტაცია",
                en: "Consultation",
                ru: "Консультация",
            }),
            description: t("about.how.item.0.description", {
                ka: "მოთხოვნების დეტალური შესწავლა და ბიზნეს-კონტექსტის გააზრება.",
                en: "A detailed review of requirements and business context.",
                ru: "Подробное изучение требований и бизнес-контекста.",
            }),
            align: "right" as const,
        },
        {
            title: t("about.how.item.1.title", {
                ka: "პროექტირება",
                en: "Design",
                ru: "Проектирование",
            }),
            description: t("about.how.item.1.description", {
                ka: "ტექნიკური არქიტექტურის დაგეგმვა, რისკების გათვალისწინებით.",
                en: "Technical architecture planning with risk considerations.",
                ru: "Планирование технической архитектуры с учетом рисков.",
            }),
            align: "left" as const,
        },
        {
            title: t("about.how.item.2.title", {
                ka: "დანერგვა",
                en: "Implementation",
                ru: "Внедрение",
            }),
            description: t("about.how.item.2.description", {
                ka: "სუფთა მონტაჟი, კონფიგურაცია, ტესტირება და ჩაბარება.",
                en: "Clean installation, configuration, testing, and handoff.",
                ru: "Аккуратный монтаж, настройка, тестирование и сдача.",
            }),
            align: "right" as const,
        },
        {
            title: t("about.how.item.3.title", {
                ka: "მხარდაჭერა",
                en: "Support",
                ru: "Поддержка",
            }),
            description: t("about.how.item.3.description", {
                ka: "პროაქტიული მონიტორინგი, მომსახურება და შემდგომი ზრდის დაგეგმვა.",
                en: "Proactive monitoring, maintenance, and future growth planning.",
                ru: "Проактивный мониторинг, обслуживание и планирование дальнейшего роста.",
            }),
            align: "left" as const,
        },
    ];

    return (
        <section className="relative overflow-hidden py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <h2 className="mb-unit-xl text-center font-headline-xl text-headline-xl text-on-surface">
                    {t("about.how.title", {
                        ka: "სამუშაო პროცესი",
                        en: "How we work",
                        ru: "Как мы работаем",
                    })}
                </h2>
                <div className="relative">
                    <div className="absolute bottom-0 left-1/2 top-0 hidden w-px bg-outline-variant/30 md:block" />
                    <div className="relative space-y-unit-lg">
                        {steps.map((step) => (
                            <HowComponent key={step.title} {...step} />
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
