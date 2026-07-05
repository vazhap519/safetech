import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function Items() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <li className="flex items-center gap-unit-sm text-on-surface">
            <span
                className="material-symbols-outlined text-secondary"
                data-icon="check_circle"
            >
                check_circle
            </span>
            {t("contact.support.item.0", {
                ka: "დისტანციური დიაგნოსტიკა და გამართვა",
                en: "Remote diagnostics and system tuning",
                ru: "Удаленная диагностика и настройка системы",
            })}
        </li>
    );
}
