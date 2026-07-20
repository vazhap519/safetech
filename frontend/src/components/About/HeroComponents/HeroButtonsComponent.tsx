import ActionLink from "@/components/ui/ActionLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroButtons() {
    const { locale, translations } = await getSiteSettings();
    const primaryLabel = translateText(
        translations,
        "about.hero.cta.primary",
        locale,
        {
            ka: "კონსულტაციის მოთხოვნა",
            en: "Request consultation",
            ru: "Запросить консультацию",
        },
    );
    const secondaryLabel = translateText(
        translations,
        "about.hero.cta.secondary",
        locale,
        {
            ka: "ჩვენი სერვისები",
            en: "Our services",
            ru: "Наши услуги",
        },
    );

    if (!primaryLabel && !secondaryLabel) return null;

    return (
        <div className="flex flex-col items-center justify-center gap-unit-md pt-4 sm:flex-row">
            {primaryLabel ? (
                <ActionLink href="/contact">{primaryLabel}</ActionLink>
            ) : null}
            {secondaryLabel ? (
                <ActionLink href="/projects" variant="glass">
                    {secondaryLabel}
                </ActionLink>
            ) : null}
        </div>
    );
}
