import NumbersComponent from "@/components/About/NumbersComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function NumbersSections() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const items = [
        {
            value: "500+",
            label: t("about.numbers.item.0.label", {
                ka: "კამერა",
                en: "Cameras",
                ru: "Камеры",
            }),
            progressClassName: "w-3/4",
        },
        {
            value: "120+",
            label: t("about.numbers.item.1.label", {
                ka: "მოწყობილობა",
                en: "Devices",
                ru: "Устройства",
            }),
            progressClassName: "w-2/3",
        },
        {
            value: "50+",
            label: t("about.numbers.item.2.label", {
                ka: "სერვერი",
                en: "Servers",
                ru: "Серверы",
            }),
            progressClassName: "w-1/2",
        },
        {
            value: "20+",
            label: t("about.numbers.item.3.label", {
                ka: "კორპორატიული პროექტი",
                en: "Enterprise projects",
                ru: "Корпоративные проекты",
            }),
            progressClassName: "w-4/5",
        },
    ];

    return (
        <section className="border-y border-outline-variant/10 bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="grid grid-cols-1 gap-gutter min-[380px]:grid-cols-2 lg:grid-cols-4">
                    {items.map((item) => (
                        <NumbersComponent key={item.value + item.label} {...item} />
                    ))}
                </div>
            </div>
        </section>
    );
}
