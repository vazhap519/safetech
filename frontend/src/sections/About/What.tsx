import WhatComponent from "@/components/About/WhatComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function WhatSection() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const items = [
        {
            indexLabel: t("about.what.item.0.index", {
                ka: "01 / ფილოსოფია",
                en: "01 / Philosophy",
                ru: "01 / Философия",
            }),
            title: t("about.what.item.0.title", {
                ka: "ინფრასტრუქტურა პირველ ადგილზე",
                en: "Infrastructure first",
                ru: "Инфраструктура прежде всего",
            }),
            description: t("about.what.item.0.description", {
                ka: "ჩვენ გვჯერა, რომ პროგრამული უზრუნველყოფა მხოლოდ მაშინ არის ეფექტური, როცა აპარატურული ნაწილი უნაკლოა.",
                en: "We believe software is only effective when the underlying hardware foundation is solid.",
                ru: "Мы считаем, что программное обеспечение эффективно только тогда, когда аппаратная основа безупречна.",
            }),
        },
        {
            indexLabel: t("about.what.item.1.index", {
                ka: "02 / სიზუსტე",
                en: "02 / Precision",
                ru: "02 / Точность",
            }),
            title: t("about.what.item.1.title", {
                ka: "დეტალებზე ორიენტირებული შესრულება",
                en: "Detail-driven execution",
                ru: "Исполнение с вниманием к деталям",
            }),
            description: t("about.what.item.1.description", {
                ka: "მონტაჟი, მარკირება, ტესტირება და დოკუმენტაცია ერთიან ხარისხის სტანდარტს მიჰყვება.",
                en: "Installation, labeling, testing, and documentation all follow one quality standard.",
                ru: "Монтаж, маркировка, тестирование и документация выполняются по единому стандарту качества.",
            }),
        },
        {
            indexLabel: t("about.what.item.2.index", {
                ka: "03 / მასშტაბირება",
                en: "03 / Scalability",
                ru: "03 / Масштабируемость",
            }),
            title: t("about.what.item.2.title", {
                ka: "მდგრადი ზრდისთვის მზად",
                en: "Ready for sustainable growth",
                ru: "Готово к устойчивому росту",
            }),
            description: t("about.what.item.2.description", {
                ka: "ყოველი არქიტექტურა იგეგმება ისე, რომ მომავალში მარტივად გაიზარდოს ახალი ფილიალებით, მომხმარებლებითა და სისტემებით.",
                en: "Every architecture is planned to grow smoothly with new branches, users, and systems.",
                ru: "Каждая архитектура проектируется так, чтобы легко масштабироваться вместе с новыми филиалами, пользователями и системами.",
            }),
        },
    ];

    return (
        <section className="bg-surface-container-low py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 gap-gutter px-margin-desktop md:grid-cols-3">
                {items.map((item) => (
                    <WhatComponent key={item.indexLabel} {...item} />
                ))}
            </div>
        </section>
    );
}
