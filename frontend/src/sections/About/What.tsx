import WhatComponent from "@/components/About/WhatComponent";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const itemIndexes = [0, 1, 2];

export default async function WhatSection() {
    const { locale, translations } = await getSiteSettings();
    const items = itemIndexes
        .map((index) => ({
            indexLabel: translateText(
                translations,
                `about.what.item.${index}.index`,
                locale,
                null,
            ),
            title: translateText(
                translations,
                `about.what.item.${index}.title`,
                locale,
                null,
            ),
            description: translateText(
                translations,
                `about.what.item.${index}.description`,
                locale,
                null,
            ),
        }))
        .filter((item) => item.indexLabel || item.title || item.description);

    if (!items.length) return null;

    return (
        <section className="bg-surface-container-low py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 gap-gutter px-margin-desktop md:grid-cols-3">
                {items.map((item) => (
                    <WhatComponent key={`${item.indexLabel}-${item.title}`} {...item} />
                ))}
            </div>
        </section>
    );
}
