import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroVerified() {
    const { locale, translations } = await getSiteSettings();

    return (
        <div className="flex items-center gap-2 rounded-xl border border-outline-variant/10 bg-surface-container-low px-4 py-2">
            <Icon className="text-[24px] text-secondary" name="verified" />
            <span className="font-label-md text-label-md">
                {translateText(translations, "services.hero.iso", locale, {
                    ka: "ISO 27001 სერტიფიცირებული",
                    en: "ISO 27001 Certified",
                    ru: "Сертификация ISO 27001",
                })}
            </span>
        </div>
    );
}
