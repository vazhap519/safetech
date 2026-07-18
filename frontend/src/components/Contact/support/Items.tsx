import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const itemIndexes = [0, 1, 2, 3, 4, 5];

export default async function Items() {
    const { locale, translations } = await getSiteSettings();
    const items = itemIndexes
        .map((index) =>
            translateText(
                translations,
                `contact.support.item.${index}`,
                locale,
                null,
            ),
        )
        .filter(Boolean);

    if (!items.length) return null;

    return (
        <>
            {items.map((item) => (
                <li className="flex items-center gap-unit-sm text-on-surface" key={item}>
                    <Icon className="text-secondary" name="check_circle" />
                    {item}
                </li>
            ))}
        </>
    );
}
