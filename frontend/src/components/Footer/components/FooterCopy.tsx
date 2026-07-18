import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FooterCopy() {
    const currentYear = new Date().getFullYear();
    const { branding, locale, translations } = await getSiteSettings();
    const rights = translateText(
        translations,
        "footer.copy.rights",
        locale,
        null,
    );

    if (!branding.siteName && !rights) {
        return null;
    }

    return (
        <p className="text-sm text-on-surface-variant opacity-80">
            {branding.siteName ? `© ${currentYear} ${branding.siteName}.` : null}
            {branding.siteName && rights ? " " : null}
            {rights}
        </p>
    );
}
