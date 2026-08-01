import NavbarClient from "@/components/Navbar/NavbarClient";
import { SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Navbar() {
    const { branding } = await getSiteSettings();

    return (
        <NavbarClient
            logo={branding.logo}
            siteName={branding.siteName || SITE_NAME}
        />
    );
}
