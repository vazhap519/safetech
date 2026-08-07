import SocialIcon from "@/components/ui/SocialIcon";
import Image from "@/components/ui/Image";
import TranslatedText from "@/components/i18n/TranslatedText";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getSiteSettings } from "@/lib/site-settings";
import { buildWhatsAppUrl } from "@/lib/whatsapp";

export default async function FooterDescription() {
    const { branding, contact, socialLinks } = await getSiteSettings();
    const siteName = branding.siteName;
    const logo = branding.footerLogo || branding.logo;
    const footerSocialLinks = socialLinks.filter(
        (item) => item.network !== "whatsapp",
    );
    const whatsappHref = contact.whatsappEnabled
        ? buildWhatsAppUrl(contact.whatsapp, contact.whatsappMessage)
        : "";

    if (whatsappHref) {
        footerSocialLinks.push({
            network: "whatsapp",
            label: "WhatsApp",
            href: whatsappHref,
            openInNewTab: true,
        });
    }

    if (!logo && !siteName && !branding.tagline && !footerSocialLinks.length) {
        return null;
    }

    return (
        <div className="space-y-4">
            <div className="space-y-3">
                {logo || siteName ? (
                    <LocalizedLink
                        aria-label={siteName || "SafeTech"}
                        className="inline-flex min-h-11 items-center"
                        href="/"
                        prefetch={false}
                    >
                        {logo ? (
                            <Image
                                alt={siteName}
                                className="h-12 w-auto object-contain"
                                height={48}
                                src={logo}
                                unoptimized
                                width={180}
                            />
                        ) : (
                            <span className="font-headline-md text-[22px] font-bold text-primary">
                                {siteName}
                            </span>
                        )}
                    </LocalizedLink>
                ) : null}
                {branding.tagline ? (
                    <p className="max-w-sm text-[15px] leading-7 text-on-surface-variant">
                        <TranslatedText
                            fallback={branding.tagline}
                            translationKey="footer.tagline"
                        />
                    </p>
                ) : null}
            </div>
            {footerSocialLinks.length ? (
                <div aria-label={siteName} className="flex flex-wrap items-center gap-3">
                    {footerSocialLinks.map(
                        ({ network, label, href, openInNewTab }, index) => (
                            <a
                                aria-label={[siteName, label]
                                    .filter(Boolean)
                                    .join(" - ")}
                                className="grid size-10 place-items-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition-all hover:-translate-y-1 hover:border-secondary/50 hover:bg-secondary/10 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                                href={href}
                                key={`${network}-${href}-${index}`}
                                rel={openInNewTab ? "noopener noreferrer" : undefined}
                                target={openInNewTab ? "_blank" : undefined}
                                title={label}
                            >
                                <SocialIcon network={network} />
                            </a>
                        ),
                    )}
                </div>
            ) : null}
        </div>
    );
}
