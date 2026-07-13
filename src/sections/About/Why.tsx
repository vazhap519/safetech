import WhyComponent from "@/components/About/WhyComponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function WhySection() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const items = [
        {
            icon: "architecture",
            title: t("about.why.item.0.title", {
                ka: "პროფესიონალური დაგეგმვა",
                en: "Professional planning",
                ru: "Профессиональное планирование",
            }),
            description: t("about.why.item.0.description", {
                ka: "ყველა პროექტი იწყება არქიტექტურით, არა იმპროვიზაციით.",
                en: "Every project starts with architecture, not improvisation.",
                ru: "Каждый проект начинается с архитектуры, а не с импровизации.",
            }),
        },
        {
            icon: "verified",
            title: t("about.why.item.1.title", {
                ka: "შემოწმებული ხარისხი",
                en: "Verified quality",
                ru: "Проверенное качество",
            }),
            description: t("about.why.item.1.description", {
                ka: "ვტესტავთ, ვამოწმებთ და ვაბარებთ მხოლოდ გამართულ სისტემას.",
                en: "We test, verify, and hand over only stable systems.",
                ru: "Мы тестируем, проверяем и сдаем только стабильные системы.",
            }),
        },
        {
            icon: "support_agent",
            title: t("about.why.item.2.title", {
                ka: "ოპერატიული მხარდაჭერა",
                en: "Responsive support",
                ru: "Оперативная поддержка",
            }),
            description: t("about.why.item.2.description", {
                ka: "დამთავრების შემდეგაც ვრჩებით პარტნიორად, არა უბრალოდ შემსრულებლად.",
                en: "We remain a partner after delivery, not just a contractor.",
                ru: "После внедрения мы остаемся партнером, а не просто подрядчиком.",
            }),
        },
        {
            icon: "hub",
            title: t("about.why.item.3.title", {
                ka: "ინტეგრირებული ხედვა",
                en: "Integrated approach",
                ru: "Интегрированный подход",
            }),
            description: t("about.why.item.3.description", {
                ka: "უსაფრთხოება, ქსელი და IT ერთიანი სტრატეგიით იმართება.",
                en: "Security, networking, and IT are managed through one strategy.",
                ru: "Безопасность, сеть и IT управляются в рамках единой стратегии.",
            }),
        },
    ];

    return (
        <section className="py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="mb-unit-xl text-center">
                    <Typography
                        as="h2"
                        variant="section-title"
                        className="mb-unit-sm font-headline-lg text-headline-lg text-on-surface"
                    >
                        {t("about.why.title", {
                            ka: "რატომ SafeTech?",
                            en: "Why SafeTech?",
                            ru: "Почему SafeTech?",
                        })}
                    </Typography>
                    <Typography
                        as="p"
                        variant="section-description"
                        className="font-body-md text-body-md text-on-surface-variant"
                    >
                        {t("about.why.description", {
                            ka: "ჩვენ ვქმნით საიმედო გარემოს თქვენი ბიზნესის განვითარებისთვის.",
                            en: "We build dependable environments for business growth.",
                            ru: "Мы создаем надежную среду для роста вашего бизнеса.",
                        })}
                    </Typography>
                </div>

                <div className="grid grid-cols-1 gap-gutter sm:grid-cols-2 lg:grid-cols-4">
                    {items.map((item) => (
                        <WhyComponent key={item.title} {...item} />
                    ))}
                </div>
            </div>
        </section>
    );
}
