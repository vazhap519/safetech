import { getSiteSettings } from "@/lib/site-settings";

export default async function FooterCopy() {
    const currentYear = new Date().getFullYear();
    const { branding } = await getSiteSettings();

    return (
        <p
            className="
                text-on-surface-variant
                opacity-80
                text-sm
            "
        >
            © {currentYear} {branding.siteName}. ყველა უფლება დაცულია.
        </p>
    );
}
