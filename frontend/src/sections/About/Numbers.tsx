import NumbersComponent from "@/components/About/NumbersComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const progressClassNames = ["w-3/4", "w-2/3", "w-1/2", "w-4/5"];

export default async function NumbersSections() {
    const { locale, translations } = await getSiteSettings();
    const items = progressClassNames
        .map((progressClassName, index) => ({
            value: translateText(
                translations,
                `about.numbers.item.${index}.value`,
                locale,
                null,
            ),
            label: translateText(
                translations,
                `about.numbers.item.${index}.label`,
                locale,
                null,
            ),
            progressClassName,
        }))
        .filter((item) => item.value || item.label);

    if (!items.length) return null;

    return (
        <section className="border-y border-outline-variant/10 bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="grid grid-cols-1 gap-gutter min-[380px]:grid-cols-2 lg:grid-cols-4">
                    {items.map((item, index) => (
                        <NumbersComponent key={`${item.value}-${index}`} {...item} />
                    ))}
                </div>
            </div>
        </section>
    );
}
