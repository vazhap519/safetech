import TranslatedText from "@/components/i18n/TranslatedText";
import { getSiteSettings } from "@/lib/site-settings";

export default async function FooterCopy() {
    const currentYear = new Date().getFullYear();
    const { branding } = await getSiteSettings();

    return (
        <p className="text-sm text-on-surface-variant opacity-80">
            {branding.siteName ? `© ${currentYear} ${branding.siteName}.` : null}
            {branding.siteName ? " " : null}
            <TranslatedText
                fallback={{
                    ka: "ყველა უფლება დაცულია.",
                    en: "All rights reserved.",
                    ru: "Все права защищены.",
                }}
                translationKey="footer.copy.rights"
            />
        </p>
    );
}
