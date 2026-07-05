import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function StoryComponentsImage() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <div className="relative group">
            <div className="absolute -inset-4 rounded-3xl bg-primary/20 blur-2xl transition-all duration-500 group-hover:bg-primary/30" />
            <Image
                variant="contact-support"
                src={branding.defaultImage}
                alt={t("about.story.imageAlt", {
                    ka: "SafeTech-ის გუნდი და კომპანიის ისტორია",
                    en: "SafeTech team and company story",
                    ru: "Команда SafeTech и история компании",
                })}
                sizes="(max-width: 768px) 100vw, 50vw"
                className="
                    h-[320px]
                    grayscale-[0.2]
                    transition-all
                    duration-700
                    group-hover:grayscale-0
                    sm:h-[420px]
                    lg:h-[500px]
                "
            />
        </div>
    );
}
