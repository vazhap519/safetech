import ActionLink from "@/components/ui/ActionLink";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function HeroButtons() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <div className="flex flex-col items-center justify-center gap-unit-md pt-4 sm:flex-row">
            <ActionLink href="/contact">
                {t("about.hero.cta.primary", {
                    ka: "მოთხოვნა",
                    en: "Request service",
                    ru: "Оставить заявку",
                })}
            </ActionLink>
            <ActionLink href="/projects" variant="glass">
                {t("about.hero.cta.secondary", {
                    ka: "ჩვენი პროექტები",
                    en: "Our projects",
                    ru: "Наши проекты",
                })}
            </ActionLink>
        </div>
    );
}
