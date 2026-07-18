import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroSupport() {
    const { locale, translations } = await getSiteSettings();
    const label = translateText(
        translations,
        "services.hero.support",
        locale,
        null,
    );

    if (!label) return null;

    return (
        <div className="flex items-center gap-2 rounded-xl border border-outline-variant/10 bg-surface-container-low px-4 py-2">
            <Icon className="text-[24px] text-secondary" name="support_agent" />
            <span className="font-label-md text-label-md">{label}</span>
        </div>
    );
}
