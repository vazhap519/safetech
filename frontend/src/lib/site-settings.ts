import "server-only";

import type { SocialNetwork } from "@/components/ui/SocialIcon";
import {
    getBackendContent,
    maybeBackendAsset,
    resolveBackendAsset,
} from "@/lib/backend";
import { getCurrentLocale } from "@/lib/locale-server";
import type { Locale } from "@/lib/locales";
import { DEFAULT_SOCIAL_IMAGE, SITE_NAME } from "@/lib/seo";
import {
    buildTranslationMap,
    type TranslationMap,
} from "@/lib/translations";

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

function pickString(
    value: unknown,
    fallback = "",
    { trim = true }: { trim?: boolean } = {},
) {
    if (typeof value !== "string") return fallback;
    return trim ? value.trim() : value;
}

export const defaultSiteContact: SiteContact = {
    phone: "",
    email: "",
    address: "",
    whatsapp: "",
    whatsappMessage: "",
    hours: "",
    leadEmail: "safetechgeorgia@gmail.com",
};

export const defaultSiteSocialLinks: SiteSocialLink[] = [];

export const defaultSiteBranding: SiteBranding = {
    siteName: SITE_NAME,
    tagline: "",
    logo: null,
    footerLogo: null,
    favicon: "/icon-192.png",
    defaultImage: DEFAULT_SOCIAL_IMAGE,
};

export async function getSiteSettings() {
    const [content, locale] = await Promise.all([
        getBackendContent(),
        getCurrentLocale(),
    ]);
    const settings = isRecord(content.settings) ? content.settings : {};
    const configuredContact = isRecord(settings.contact) ? settings.contact : {};
    const configuredBranding = isRecord(settings.branding)
        ? settings.branding
        : {};
    const configuredSeo = isRecord(settings.seo) ? settings.seo : {};
    const translations = buildTranslationMap(settings.translations);

    const socialLinks = parseSocialLinks(
        settings.socials,
        defaultSiteSocialLinks,
    );

    const contact = {
        phone: pickString(configuredContact.phone, defaultSiteContact.phone),
        email: pickString(configuredContact.email, defaultSiteContact.email),
        address: pickString(
            configuredContact.address,
            defaultSiteContact.address,
        ),
        whatsapp: pickString(
            configuredContact.whatsapp,
            defaultSiteContact.whatsapp,
        ),
        whatsappMessage: pickString(
            configuredContact.whatsapp_message,
            defaultSiteContact.whatsappMessage,
            { trim: false },
        ),
        hours: pickString(configuredContact.hours, defaultSiteContact.hours),
        leadEmail:
            pickString(configuredContact.lead_email, defaultSiteContact.leadEmail) ||
            defaultSiteContact.leadEmail,
    } satisfies SiteContact;

    const branding = {
        siteName:
            pickString(configuredBranding.site_name) ||
            pickString(configuredSeo.site_name) ||
            defaultSiteBranding.siteName,
        tagline: pickString(configuredBranding.tagline),
        logo: maybeBackendAsset(
            pickString(configuredBranding.logo) || null,
        ),
        footerLogo: maybeBackendAsset(
            pickString(configuredBranding.footer_logo) ||
                pickString(configuredBranding.logo) ||
                null,
        ),
        favicon: resolveBackendAsset(
            pickString(configuredBranding.favicon) || null,
            defaultSiteBranding.favicon,
        ),
        defaultImage: resolveBackendAsset(
            pickString(configuredBranding.default_image) ||
                pickString(configuredSeo.default_image) ||
                null,
            defaultSiteBranding.defaultImage,
        ),
    } satisfies SiteBranding;

    return {
        contact,
        socialLinks,
        branding,
        locale: locale satisfies Locale,
        translations: translations satisfies TranslationMap,
    };
}
