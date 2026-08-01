import "server-only";

import { cache } from "react";
import type { SocialNetwork } from "@/components/ui/SocialIcon";
import {
    getBackendContent,
    maybeBackendAsset,
    resolveBackendAsset,
} from "@/lib/backend";
import { getCurrentLocale } from "@/lib/locale-server";
import type { Locale } from "@/lib/locales";
import {
    buildTranslationMap,
    type TranslationMap,
} from "@/lib/translations";

type SiteContact = {
    phone: string;
    phones: string[];
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

type SiteBranding = {
    siteName: string;
    tagline: string;
    logo: string | null;
    footerLogo: string | null;
    favicon: string;
    defaultImage: string | null;
};

type SiteSeoSettings = {
    defaultKeywords: string[];
    robotsIndex: boolean;
    robotsFollow: boolean;
};

type SiteIntegrations = {
    marketingEnabled: boolean;
    googleTagManagerId: string;
    googleAnalyticsId: string;
    metaPixelId: string;
    googleSiteVerification: string;
    bingSiteVerification: string;
    yandexSiteVerification: string;
    indexNowKey: string;
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

function normalizeSocialNetwork(value: string): SocialNetwork | null {
    const normalized = value.trim().toLowerCase();

    if (normalized === "twitter") return "x";

    return isSocialNetwork(normalized) ? normalized : null;
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
                const network = normalizeSocialNetwork(item.network);
                if (!network) return null;
                const href = typeof item.href === "string"
                    ? normalizeSocialHref(network, item.href)
                    : "";
                if (!href) return null;

                return {
                    network,
                    label:
                        typeof item.label === "string" && item.label.trim()
                            ? item.label.trim()
                            : fallbackLabel(network),
                    href,
                } satisfies SiteSocialLink;
            })
            .filter((item): item is SiteSocialLink => Boolean(item));

        if (links.length) return links;
    }

    if (isRecord(value)) {
        const legacyLinks = Object.entries(value)
            .map(([network, href]) => {
                const normalizedNetwork = normalizeSocialNetwork(network);
                if (!normalizedNetwork || typeof href !== "string") return null;
                const normalizedHref = normalizeSocialHref(normalizedNetwork, href);
                if (!normalizedHref) return null;

                return {
                    network: normalizedNetwork,
                    label: fallbackLabel(normalizedNetwork),
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

function normalizeStringList(value: unknown) {
    const values = Array.isArray(value)
        ? value
              .map((item) => {
                  if (typeof item === "string") return item;
                  if (isRecord(item) && typeof item.value === "string") {
                      return item.value;
                  }
                  return "";
              })
              .map((item) => item.trim())
              .filter(Boolean)
        : [];

    return [...new Set(values)];
}

const defaultSiteContact: SiteContact = {
    phone: "",
    phones: [],
    email: "",
    address: "",
    whatsapp: "",
    whatsappMessage: "",
    hours: "",
    leadEmail: "safetechgeorgia@gmail.com",
};

const defaultSiteSocialLinks: SiteSocialLink[] = [];

const defaultSiteBranding: SiteBranding = {
    siteName: "",
    tagline: "",
    logo: null,
    footerLogo: null,
    favicon: "/icon-192.png",
    defaultImage: null,
};

const defaultSiteSeo: SiteSeoSettings = {
    defaultKeywords: [],
    robotsIndex: true,
    robotsFollow: true,
};

export const getSiteSettings = cache(async () => {
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
    const configuredIntegrations = isRecord(settings.integrations)
        ? settings.integrations
        : {};
    const translations = buildTranslationMap(settings.translations);
    const socialLinks = parseSocialLinks(settings.socials, defaultSiteSocialLinks);
    const configuredPhones = normalizeStringList(configuredContact.phones);
    const phoneCandidates = [
        pickString(configuredContact.phone, defaultSiteContact.phone),
        ...configuredPhones,
    ].filter(Boolean);
    const phones = [...new Set(phoneCandidates)];

    const contact = {
        phone: phones[0] ?? defaultSiteContact.phone,
        phones,
        email: pickString(configuredContact.email, defaultSiteContact.email),
        address: pickString(configuredContact.address, defaultSiteContact.address),
        whatsapp: pickString(configuredContact.whatsapp, defaultSiteContact.whatsapp),
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
        logo: maybeBackendAsset(pickString(configuredBranding.logo) || null),
        footerLogo: maybeBackendAsset(
            pickString(configuredBranding.footer_logo) ||
                pickString(configuredBranding.logo) ||
                null,
        ),
        favicon: resolveBackendAsset(
            pickString(configuredBranding.favicon) || null,
            defaultSiteBranding.favicon,
        ),
        defaultImage: maybeBackendAsset(
            pickString(configuredBranding.default_image) ||
                pickString(configuredSeo.default_image) ||
                null,
        ),
    } satisfies SiteBranding;

    const integrations = {
        marketingEnabled:
            configuredIntegrations.marketing_enabled === true ||
            configuredIntegrations.marketing_enabled === "true" ||
            configuredIntegrations.marketing_enabled === 1,
        googleTagManagerId: pickString(configuredIntegrations.google_tag_manager_id),
        googleAnalyticsId: pickString(configuredIntegrations.google_analytics_id),
        metaPixelId: pickString(configuredIntegrations.meta_pixel_id),
        googleSiteVerification: pickString(
            configuredIntegrations.google_site_verification,
        ),
        bingSiteVerification: pickString(
            configuredIntegrations.bing_site_verification,
        ),
        yandexSiteVerification: pickString(
            configuredIntegrations.yandex_site_verification,
        ),
        indexNowKey: pickString(configuredIntegrations.indexnow_key),
    } satisfies SiteIntegrations;

    const seo = {
        defaultKeywords: normalizeStringList(configuredSeo.default_keywords),
        robotsIndex:
            configuredSeo.robots_index === undefined
                ? defaultSiteSeo.robotsIndex
                : configuredSeo.robots_index === true ||
                  configuredSeo.robots_index === "true" ||
                  configuredSeo.robots_index === 1,
        robotsFollow:
            configuredSeo.robots_follow === undefined
                ? defaultSiteSeo.robotsFollow
                : configuredSeo.robots_follow === true ||
                  configuredSeo.robots_follow === "true" ||
                  configuredSeo.robots_follow === 1,
    } satisfies SiteSeoSettings;

    return {
        contact,
        socialLinks,
        branding,
        seo,
        integrations,
        locale: locale satisfies Locale,
        translations: translations satisfies TranslationMap,
    };
});
