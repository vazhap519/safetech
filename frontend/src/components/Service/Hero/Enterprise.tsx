import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Enterprise() {
    const { locale, translations } = await getSiteSettings();
    const eyebrow = translateText(
        translations,
        "services.hero.eyebrow",
        locale,
        null,
    );

    if (!eyebrow) return null;

    return (
        <div className="mb-unit-md inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary-container/10 px-3 py-1">
            <span className="flex h-2 w-2 animate-pulse rounded-full bg-primary" />
            <span className="font-mono-sm text-mono-sm uppercase tracking-widest text-primary">
                {eyebrow}
            </span>
        </div>
    );
}
