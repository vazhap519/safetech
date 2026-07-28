import NavbarClient from "@/components/Navbar/NavbarClient";
import { SITE_NAME } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Navbar() {
    const { branding, features } = await getSiteSettings();

    return (
        <NavbarClient
            logo={branding.logo}
            showShop={features.shopEnabled}
            siteName={branding.siteName || SITE_NAME}
        />
    );
}
