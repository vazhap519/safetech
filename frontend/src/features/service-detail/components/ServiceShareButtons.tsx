import SocialIcon, { type SocialNetwork } from "@/components/ui/SocialIcon";
import type { Locale } from "@/lib/locales";
import { absoluteLocalizedUrl } from "@/lib/seo";

type ServiceShareButtonsProps = {
    locale: Locale;
    shareLabel: string;
    service: {
        slug: string;
        name: string;
    };
};

type ShareLink = {
    network: SocialNetwork;
    label: string;
    href: string;
};

export default function ServiceShareButtons({
    locale,
    shareLabel,
    service,
}: ServiceShareButtonsProps) {
    const url = absoluteLocalizedUrl(`/services/${service.slug}`, locale);
    const encodedUrl = encodeURIComponent(url);
    const encodedTitle = encodeURIComponent(service.name);
    const encodedText = encodeURIComponent(`${service.name} - ${url}`);

    const links: ShareLink[] = [
        {
            network: "facebook",
            label: "Facebook",
            href: `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`,
        },
        {
            network: "linkedin",
            label: "LinkedIn",
            href: `https://www.linkedin.com/sharing/share-offsite/?url=${encodedUrl}`,
        },
        {
            network: "whatsapp",
            label: "WhatsApp",
            href: `https://wa.me/?text=${encodedText}`,
        },
        {
            network: "x",
            label: "X",
            href: `https://twitter.com/intent/tweet?text=${encodedTitle}&url=${encodedUrl}`,
        },
        {
            network: "telegram",
            label: "Telegram",
            href: `https://t.me/share/url?url=${encodedUrl}&text=${encodedTitle}`,
        },
        {
            network: "email",
            label: "Email",
            href: `mailto:?subject=${encodedTitle}&body=${encodedText}`,
        },
    ];

    return (
        <div className="space-y-3 pt-unit-sm">
            <p className="font-label-md text-label-md uppercase tracking-[0.2em] text-on-surface-variant">
                {shareLabel}
            </p>
            <div className="flex flex-wrap gap-3">
                {links.map(({ network, label, href }) => {
                    const isExternal = !href.startsWith("mailto:");

                    return (
                        <a
                            aria-label={`${service.name} - ${label}`}
                            className="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-on-surface transition-all hover:-translate-y-0.5 hover:border-secondary/40 hover:bg-secondary/10 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                            href={href}
                            key={network}
                            rel={isExternal ? "noopener noreferrer" : undefined}
                            target={isExternal ? "_blank" : undefined}
                        >
                            <SocialIcon className="size-4" network={network} />
                        </a>
                    );
                })}
            </div>
        </div>
    );
}
