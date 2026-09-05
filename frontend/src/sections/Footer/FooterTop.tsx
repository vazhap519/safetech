import FooterDescription from "@/components/Footer/components/FooterDescription";
import FooterServices from "@/components/Footer/components/FooterServices";
import FooterCompany from "@/components/Footer/components/FooterCompany";
import FooterContact from "@/components/Footer/components/FooterContact";
import type { FooterServiceLink } from "@/lib/backend";
import type { SiteSocialLink } from "@/lib/site-settings";

type FooterBranding = {
    siteName: string;
    tagline: string;
    logo: string | null;
    footerLogo: string | null;
};

type FooterContactDetails = {
    phone: string;
    phones: string[];
    email: string;
    address: string;
    whatsapp: string;
    whatsappEnabled: boolean;
    whatsappMessage: string;
};

type FooterTopProps = {
    branding: FooterBranding;
    contact: FooterContactDetails;
    services: FooterServiceLink[];
    socialLinks: SiteSocialLink[];
};

export default function FooterTop({
    branding,
    contact,
    services,
    socialLinks,
}: FooterTopProps) {
    return (
        <div className="footer-grid mx-auto grid max-w-container-max gap-10 px-5 md:px-8 xl:px-14">
            <FooterDescription
                branding={branding}
                contact={contact}
                socialLinks={socialLinks}
            />
            <FooterServices services={services} />
            <FooterCompany />
            <FooterContact contact={contact} />
        </div>
    );
}
