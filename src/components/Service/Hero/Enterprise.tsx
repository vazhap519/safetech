import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Enterprise(){
    const { locale, translations } = await getSiteSettings();

    return (
        <div
            className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary-container/10 border border-primary/20 mb-unit-md">
            <span className="flex h-2 w-2 rounded-full bg-primary animate-pulse"></span>
            <span className="font-mono-sm text-mono-sm text-primary uppercase tracking-widest">
                {translateText(translations, "services.hero.eyebrow", locale, {
                    ka: "უსაფრთხოების სერვისები",
                    en: "Enterprise Security Solutions",
                    ru: "Решения для безопасности",
                })}
            </span>
        </div>
    )
}
