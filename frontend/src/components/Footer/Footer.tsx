import FooterTop from "@/sections/Footer/FooterTop";
import FooterBottom from "@/sections/Footer/footerBottom";
import { getBackendFooterServices } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Footer({
    marketingEnabled = false,
}: {
    marketingEnabled?: boolean;
}) {
    const [{ branding, contact, socialLinks }, services] = await Promise.all([
        getSiteSettings(),
        getBackendFooterServices(),
    ]);

    return (
        <footer className="mt-16 w-full border-t border-outline-variant/10 bg-surface-container-lowest py-12 md:mt-20 md:py-16">
            <FooterTop
                branding={branding}
                contact={contact}
                services={services}
                socialLinks={socialLinks}
            />
            <FooterBottom marketingEnabled={marketingEnabled} />
        </footer>
    );
}
