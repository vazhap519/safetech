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
    selectClientTranslations,
    type TranslationMap,
} from "@/lib/translations";

type SiteContact = {
    phone: string;
    phones: string[];
    email: string;
    address: string;
    whatsapp: string;
    whatsappEnabled: boolean;
    whatsappMessage: string;
    hours: string;
    leadEmail: string;
};

export type SiteSocialLink = {
    network: SocialNetwork;
    label: string;
    href: string;
    openInNewTab: boolean;
};

export function isOrganizationSocialLink(network: SocialNetwork): boolean {
    return !["email", "viber", "whatsapp"].includes(network);
}

export type ShareButtonType =
    | "facebook"
    | "whatsapp"
    | "telegram"
    | "linkedin"
    | "x"
    | "pinterest"
    | "viber"
    | "email"
    | "native"
    | "copy";

export type SiteShareButton = {
    type: ShareButtonType;
    label: string;
};

export type SiteSocialSharing = {
    enabled: boolean;
    showOnServices: boolean;
    showOnProjects: boolean;
    title: string;
    buttons: SiteShareButton[];
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
    googleReviewUrl: string;
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
        "viber",
        "pinterest",
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

function normalizeShareButtonType(value: string): ShareButtonType | null {
    const normalized = value.trim().toLowerCase();

    if (normalized === "twitter") return "x";
    if (normalized === "link") return "copy";
    if (normalized === "share") return "native";

    return [
        "facebook",
        "whatsapp",
        "telegram",
        "linkedin",
        "x",
        "pinterest",
        "viber",
        "email",
        "native",
        "copy",
    ].includes(normalized)
        ? (normalized as ShareButtonType)
        : null;
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
        case "viber":
            return "Viber";
        case "pinterest":
            return "Pinterest";
        case "email":
            return "Email";
        default:
            return network.charAt(0).toUpperCase() + network.slice(1);
    }
}

function fallbackShareTitle(locale: Locale) {
    switch (locale) {
        case "en":
            return "Share";
        case "ru":
            return "Поделиться";
        default:
            return "გაზიარება";
    }
}

function normalizeBoolean(value: unknown, fallback: boolean) {
    if (typeof value === "boolean") return value;
    if (typeof value === "number") return value === 1;
    if (typeof value === "string") {
        const normalized = value.trim().toLowerCase();
        if (["1", "true", "yes", "on"].includes(normalized)) return true;
        if (["0", "false", "no", "off", ""].includes(normalized)) return false;
    }

    return fallback;
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
    if (network === "viber") {
        if (trimmed.startsWith("http") || trimmed.startsWith("viber:")) {
            return trimmed;
        }
        const digits = trimmed.replace(/[^\d]/g, "");
        return digits ? `viber://chat?number=%2B${digits}` : "";
    }
    if (trimmed.startsWith("http://") || trimmed.startsWith("https://")) {
        return trimmed;
    }

    return `https://${trimmed}`;
}

function parseSocialLinks(value: unknown, fallbackLinks: SiteSocialLink[]) {
    if (isRecord(value) && Array.isArray(value.links)) {
        const links = value.links
            .map((item): SiteSocialLink | null => {
                if (
                    !isRecord(item) ||
                    typeof item.network !== "string" ||
                    !normalizeBoolean(item.enabled, true)
                ) {
                    return null;
                }
                const network = normalizeSocialNetwork(item.network);
                if (!network) return null;
                const href =
                    typeof item.href === "string"
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
                    openInNewTab: normalizeBoolean(item.open_in_new_tab, true),
                };
            })
            .filter((item): item is SiteSocialLink => Boolean(item));

        if (links.length) return links;
    }

    if (isRecord(value)) {
        const legacyLinks = Object.entries(value)
            .map(([network, href]): SiteSocialLink | null => {
                const normalizedNetwork = normalizeSocialNetwork(network);
                if (!normalizedNetwork || typeof href !== "string") return null;
                const normalizedHref = normalizeSocialHref(normalizedNetwork, href);
                if (!normalizedHref) return null;

                return {
                    network: normalizedNetwork,
                    label: fallbackLabel(normalizedNetwork),
                    href: normalizedHref,
                    openInNewTab: true,
                };
            })
            .filter((item): item is SiteSocialLink => Boolean(item));

        if (legacyLinks.length) return legacyLinks;
    }

    return fallbackLinks;
}

function parseSocialSharing(value: unknown, locale: Locale): SiteSocialSharing {
    const configured = isRecord(value) ? value : {};
    const configuredButtons = Array.isArray(configured.share_buttons)
        ? configured.share_buttons
        : [];
    const seenTypes = new Set<ShareButtonType>();
    const buttons = configuredButtons
        .map((item) => {
            const record = isRecord(item) ? item : null;
            const rawType =
                typeof item === "string"
                    ? item
                    : typeof record?.type === "string"
                      ? record.type
                      : typeof record?.name === "string"
                        ? record.name
                        : "";
            const type = normalizeShareButtonType(rawType);

            if (!type || !normalizeBoolean(record?.enabled, true) || seenTypes.has(type)) {
                return null;
            }

            seenTypes.add(type);

            return {
                type,
                label:
                    typeof record?.label === "string" ? record.label.trim() : "",
            } satisfies SiteShareButton;
        })
        .filter((item): item is SiteShareButton => Boolean(item));
    const localizedTitle = configured[`share_title_${locale}`];
    const legacyTitle = configured.share_title;

    return {
        enabled: normalizeBoolean(configured.share_enabled, true),
        showOnServices: normalizeBoolean(configured.share_on_services, true),
        showOnProjects: normalizeBoolean(configured.share_on_projects, true),
        title:
            (typeof localizedTitle === "string" && localizedTitle.trim()) ||
            (typeof legacyTitle === "string" && legacyTitle.trim()) ||
            fallbackShareTitle(locale),
        buttons,
    };
}

function pickString(
    value: unknown,
    fallback = "",
    { trim = true }: { trim?: boolean } = {},
) {
    if (typeof value !== "string") return fallback;
    return trim ? value.trim() : value;
}

function pickHttpUrl(value: unknown): string {
    const candidate = pickString(value);
    if (!candidate) return "";

    try {
        const url = new URL(candidate);

        return url.protocol === "https:" || url.protocol === "http:"
            ? url.toString()
            : "";
    } catch {
        return "";
    }
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
    whatsappEnabled: false,
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
    const clientTranslations = selectClientTranslations(
        buildTranslationMap(
            settings.client_translations ?? settings.translations,
        ),
    );
    const socialSharing = parseSocialSharing(settings.socials, locale);
    const configuredPhones = normalizeStringList(configuredContact.phones);
    const phoneCandidates = [
        pickString(configuredContact.phone, defaultSiteContact.phone),
        ...configuredPhones,
    ].filter(Boolean);
    const phones = [...new Set(phoneCandidates)];

    const whatsapp = pickString(
        configuredContact.whatsapp,
        defaultSiteContact.whatsapp,
    );
    const contact = {
        phone: phones[0] ?? defaultSiteContact.phone,
        phones,
        email: pickString(configuredContact.email, defaultSiteContact.email),
        address: pickString(configuredContact.address, defaultSiteContact.address),
        whatsapp,
        whatsappEnabled: whatsapp
            ? normalizeBoolean(configuredContact.whatsapp_enabled, true)
            : false,
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
    const socialLinks = parseSocialLinks(settings.socials, defaultSiteSocialLinks);

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
        googleReviewUrl: pickHttpUrl(configuredIntegrations.google_review_url),
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
        socialSharing,
        branding,
        seo,
        integrations,
        locale: locale satisfies Locale,
        translations: translations satisfies TranslationMap,
        clientTranslations: clientTranslations satisfies TranslationMap,
    };
});
