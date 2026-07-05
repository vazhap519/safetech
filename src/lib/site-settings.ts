import "server-only";

import type { SocialNetwork } from "@/components/ui/SocialIcon";
import {
    getBackendContent,
    maybeBackendAsset,
    resolveBackendAsset,
} from "@/lib/backend";
import { DEFAULT_SOCIAL_IMAGE, SITE_NAME } from "@/lib/seo";

export type SiteContact = {
    phone: string;
    email: string;
    address: string;
    whatsapp: string;
    whatsappMessage: string;
    hours: string;
    leadEmail: string;
};

export type SiteSocialLink = {
    network: SocialNetwork;
    label: string;
    href: string;
};

export type SiteBranding = {
    siteName: string;
    tagline: string;
    logo: string | null;
    footerLogo: string | null;
    favicon: string;
    defaultImage: string;
};

function isRecord(value: unknown): value is Record<string, unknown> {
    return typeof value === "object" && value !== null && !Array.isArray(value);
}

function isSocialNetwork(value: string): value is SocialNetwork {
    return [
        "facebook",
        "linkedin",
        "instagram",
        "tiktok",
        "whatsapp",
        "email",
        "x",
        "youtube",
        "telegram",
    ].includes(value);
}

function fallbackLabel(network: SocialNetwork) {
    switch (network) {
        case "x":
            return "X";
        case "youtube":
            return "YouTube";
        case "telegram":
            return "Telegram";
        case "whatsapp":
            return "WhatsApp";
        case "email":
            return "Email";
        default:
            return network.charAt(0).toUpperCase() + network.slice(1);
    }
}

function normalizeSocialHref(network: SocialNetwork, href: string) {
    const trimmed = href.trim();

    if (!trimmed) return "";

    if (network === "email") {
        return trimmed.startsWith("mailto:") ? trimmed : `mailto:${trimmed}`;
    }

    if (network === "whatsapp") {
        if (trimmed.startsWith("http")) return trimmed;

        const digits = trimmed.replace(/[^\d]/g, "");
        return digits ? `https://wa.me/${digits}` : "";
    }

    if (trimmed.startsWith("http://") || trimmed.startsWith("https://")) {
        return trimmed;
    }

    return `https://${trimmed}`;
}

function parseSocialLinks(value: unknown, fallbackLinks: SiteSocialLink[]) {
    if (isRecord(value) && Array.isArray(value.links)) {
        const links = value.links
            .map((item) => {
                if (!isRecord(item) || typeof item.network !== "string") {
                    return null;
                }

                if (!isSocialNetwork(item.network)) {
                    return null;
                }

                const href =
                    typeof item.href === "string"
                        ? normalizeSocialHref(item.network, item.href)
                        : "";

                if (!href) return null;

                return {
                    network: item.network,
                    label:
                        typeof item.label === "string" && item.label.trim()
                            ? item.label.trim()
                            : fallbackLabel(item.network),
                    href,
                } satisfies SiteSocialLink;
            })
            .filter((item): item is SiteSocialLink => Boolean(item));

        if (links.length) return links;
    }

    if (isRecord(value)) {
        const legacyLinks = Object.entries(value)
            .map(([network, href]) => {
                if (!isSocialNetwork(network) || typeof href !== "string") {
                    return null;
                }

                const normalizedHref = normalizeSocialHref(network, href);

                if (!normalizedHref) return null;

                return {
                    network,
                    label: fallbackLabel(network),
                    href: normalizedHref,
                } satisfies SiteSocialLink;
            })
            .filter((item): item is SiteSocialLink => Boolean(item));

        if (legacyLinks.length) return legacyLinks;
    }

    return fallbackLinks;
}

export const defaultSiteContact: SiteContact = {
    phone: "+995 32 2 00 00 00",
    email: "info@safetech.ge",
    address: "თბილისი, საქართველო",
    whatsapp: "995599123456",
    whatsappMessage: "გამარჯობა, მაინტერესებს SafeTech-ის მომსახურება.",
    hours: "ორშ - პარ: 10:00 - 19:00",
    leadEmail: "safetechgeorgia@gmail.com",
};

export const defaultSiteSocialLinks: SiteSocialLink[] = [
    {
        network: "facebook",
        label: "Facebook",
        href: "https://www.facebook.com/",
    },
    {
        network: "linkedin",
        label: "LinkedIn",
        href: "https://www.linkedin.com/",
    },
    {
        network: "instagram",
        label: "Instagram",
        href: "https://www.instagram.com/",
    },
    {
        network: "tiktok",
        label: "TikTok",
        href: "https://www.tiktok.com/",
    },
];

export const defaultSiteBranding: SiteBranding = {
    siteName: SITE_NAME,
    tagline: "თქვენი ბიზნესის ტექნოლოგიური და უსაფრთხოების გარანტი.",
    logo: null,
    footerLogo: null,
    favicon: "/icon-192.png",
    defaultImage: DEFAULT_SOCIAL_IMAGE,
};

export async function getSiteSettings() {
    const content = await getBackendContent();
    const settings = isRecord(content.settings) ? content.settings : {};
    const configuredContact = isRecord(settings.contact) ? settings.contact : {};
    const configuredBranding = isRecord(settings.branding)
        ? settings.branding
        : {};
    const configuredSeo = isRecord(settings.seo) ? settings.seo : {};

    const socialLinks = parseSocialLinks(
        settings.socials,
        defaultSiteSocialLinks,
    );

    const contact = {
        ...defaultSiteContact,
        ...configuredContact,
    } satisfies SiteContact;

    const branding = {
        siteName:
            typeof configuredBranding.site_name === "string" &&
            configuredBranding.site_name.trim()
                ? configuredBranding.site_name.trim()
                : typeof configuredSeo.site_name === "string" &&
                    configuredSeo.site_name.trim()
                  ? configuredSeo.site_name.trim()
                  : defaultSiteBranding.siteName,
        tagline:
            typeof configuredBranding.tagline === "string" &&
            configuredBranding.tagline.trim()
                ? configuredBranding.tagline.trim()
                : defaultSiteBranding.tagline,
        logo: maybeBackendAsset(
            typeof configuredBranding.logo === "string"
                ? configuredBranding.logo
                : null,
        ),
        footerLogo: maybeBackendAsset(
            typeof configuredBranding.footer_logo === "string"
                ? configuredBranding.footer_logo
                : typeof configuredBranding.logo === "string"
                  ? configuredBranding.logo
                  : null,
        ),
        favicon: resolveBackendAsset(
            typeof configuredBranding.favicon === "string"
                ? configuredBranding.favicon
                : null,
            defaultSiteBranding.favicon,
        ),
        defaultImage: resolveBackendAsset(
            typeof configuredBranding.default_image === "string"
                ? configuredBranding.default_image
                : typeof configuredSeo.default_image === "string"
                  ? configuredSeo.default_image
                  : null,
            defaultSiteBranding.defaultImage,
        ),
    } satisfies SiteBranding;

    return {
        contact,
        socialLinks,
        branding,
    };
}
