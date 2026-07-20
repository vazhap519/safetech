import SocialIcon from "@/components/ui/SocialIcon";
import Image from "@/components/ui/Image";
import TranslatedText from "@/components/i18n/TranslatedText";
import { getSiteSettings } from "@/lib/site-settings";

export default async function FooterDescription() {
    const { branding, socialLinks } = await getSiteSettings();
    const siteName = branding.siteName;
    const logo = branding.footerLogo || branding.logo;

    if (!logo && !siteName && !branding.tagline && !socialLinks.length) {
        return null;
    }

    return (
        <div className="space-y-4">
            <div className="space-y-3">
                {logo ? (
                    <Image
                        alt={siteName}
                        className="h-12 w-auto object-contain"
                        height={48}
                        src={logo}
                        unoptimized
                        width={180}
                    />
                ) : siteName ? (
                    <span className="font-headline-md text-headline-md font-bold text-primary">
                        {siteName}
                    </span>
                ) : null}
                {branding.tagline ? (
                    <p className="font-body-md text-body-md text-on-surface-variant">
                        <TranslatedText
                            fallback={branding.tagline}
                            translationKey="footer.tagline"
                        />
                    </p>
                ) : null}
            </div>
            {socialLinks.length ? (
                <div aria-label={siteName} className="flex flex-wrap items-center gap-3">
                    {socialLinks.map(({ network, label, href }, index) => (
                        <a
                            aria-label={[siteName, label].filter(Boolean).join(" - ")}
                            className="grid size-10 place-items-center rounded-full border border-white/10 bg-white/5 text-on-surface-variant transition-all hover:-translate-y-1 hover:border-secondary/50 hover:bg-secondary/10 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            href={href}
                            key={`${network}-${href}-${index}`}
                            rel="noopener noreferrer"
                            target="_blank"
                            title={label}
                        >
                            <SocialIcon network={network} />
                        </a>
                    ))}
                </div>
            ) : null}
        </div>
    );
}
