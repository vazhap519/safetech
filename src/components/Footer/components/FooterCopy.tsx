import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FooterCopy() {
    const currentYear = new Date().getFullYear();
    const { branding, locale, translations } = await getSiteSettings();

    return (
        <p
            className="
                text-on-surface-variant
                opacity-80
                text-sm
            "
        >
            © {currentYear} {branding.siteName}.{" "}
            {translateText(translations, "footer.copy.rights", locale, {
                ka: "ყველა უფლება დაცულია.",
                en: "All rights reserved.",
                ru: "Все права защищены.",
            })}
        </p>
    );
}
