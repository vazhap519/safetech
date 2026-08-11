"use client";

import { hasAnalyticsConsent } from "@/lib/consent";

type EventParameters = Record<string, string | number | boolean | undefined>;

type CampaignParameter =
    | "utm_source"
    | "utm_medium"
    | "utm_campaign"
    | "utm_content"
    | "utm_term";

type CampaignAttribution = Partial<Record<CampaignParameter, string>> & {
    landing_page?: string;
    referrer_host?: string;
};

export type LeadAttributionDetail = {
    key: string;
    label: string;
    type: "attribution";
    value: string;
};

type GoogleConsentParameters = {
    ad_storage: "granted" | "denied";
    ad_user_data: "granted" | "denied";
    ad_personalization: "granted" | "denied";
    analytics_storage: "granted" | "denied";
};

interface GoogleTagFunction {
    (command: "event", name: string, parameters?: EventParameters): void;
    (
        command: "consent",
        action: "default" | "update",
        parameters: GoogleConsentParameters,
    ): void;
}

const CAMPAIGN_STORAGE_KEY = "safetech_campaign_attribution";
const CAMPAIGN_PARAMETERS: readonly CampaignParameter[] = [
    "utm_source",
    "utm_medium",
    "utm_campaign",
    "utm_content",
    "utm_term",
];
let campaignAttributionCache: CampaignAttribution | null = null;

function safeText(value: string | null | undefined, maxLength = 160) {
    return (value ?? "")
        .replace(/[\u0000-\u001F\u007F]/g, "")
        .trim()
        .slice(0, maxLength);
}

function currentCampaignAttribution(): CampaignAttribution {
    if (typeof window === "undefined") return {};

    const query = new URLSearchParams(window.location.search);
    const attribution: CampaignAttribution = {
        landing_page: safeText(window.location.pathname, 300),
    };

    for (const parameter of CAMPAIGN_PARAMETERS) {
        const value = safeText(query.get(parameter));

        if (value) attribution[parameter] = value;
    }

    if (document.referrer) {
        try {
            const referrer = new URL(document.referrer);

            if (referrer.hostname !== window.location.hostname) {
                attribution.referrer_host = safeText(referrer.hostname);
            }
        } catch {
            // Ignore malformed or privacy-redacted referrers.
        }
    }

    return attribution;
}

function readStoredCampaignAttribution(): CampaignAttribution {
    if (campaignAttributionCache) return campaignAttributionCache;

    try {
        const value = window.sessionStorage.getItem(CAMPAIGN_STORAGE_KEY);
        const parsed = value ? (JSON.parse(value) as unknown) : null;

        if (!parsed || typeof parsed !== "object" || Array.isArray(parsed)) {
            campaignAttributionCache = {};

            return campaignAttributionCache;
        }

        const record = parsed as Record<string, unknown>;
        const attribution: CampaignAttribution = {};

        for (const key of [
            ...CAMPAIGN_PARAMETERS,
            "landing_page",
            "referrer_host",
        ] as const) {
            if (typeof record[key] === "string") {
                const value = safeText(
                    record[key],
                    key === "landing_page" ? 300 : 160,
                );

                if (value) attribution[key] = value;
            }
        }

        campaignAttributionCache = attribution;

        return campaignAttributionCache;
    } catch {
        campaignAttributionCache = {};

        return campaignAttributionCache;
    }
}

export function captureCampaignAttribution(): CampaignAttribution {
    if (typeof window === "undefined" || !hasAnalyticsConsent()) return {};

    const attribution = {
        ...currentCampaignAttribution(),
        ...readStoredCampaignAttribution(),
    } satisfies CampaignAttribution;
    campaignAttributionCache = attribution;

    try {
        window.sessionStorage.setItem(
            CAMPAIGN_STORAGE_KEY,
            JSON.stringify(attribution),
        );
    } catch {
        // Analytics continues even when browser storage is unavailable.
    }

    return attribution;
}

export function withAnalyticsContext(
    parameters: EventParameters = {},
): EventParameters {
    if (typeof window === "undefined") return parameters;

    return {
        page_path: safeText(window.location.pathname, 300),
        language: safeText(document.documentElement.lang, 20),
        ...captureCampaignAttribution(),
        ...parameters,
    };
}

export function getLeadAttributionDetails(): LeadAttributionDetail[] {
    if (typeof window === "undefined") return [];

    const attribution = hasAnalyticsConsent()
        ? {
              ...currentCampaignAttribution(),
              ...readStoredCampaignAttribution(),
          }
        : {};
    const values: Array<[string, string, string | undefined]> = [
        ["submitted_page", "Submitted from", window.location.pathname],
        ["landing_page", "Landing page", attribution.landing_page],
        ["utm_source", "Campaign source", attribution.utm_source],
        ["utm_medium", "Campaign medium", attribution.utm_medium],
        ["utm_campaign", "Campaign name", attribution.utm_campaign],
        ["utm_content", "Campaign content", attribution.utm_content],
        ["utm_term", "Campaign term", attribution.utm_term],
        ["referrer_host", "Referrer", attribution.referrer_host],
    ];

    return values
        .map(([key, label, rawValue]) => ({
            key: `attribution_${key}`,
            label,
            type: "attribution" as const,
            value: safeText(rawValue, key.includes("page") ? 300 : 160),
        }))
        .filter((detail) => detail.value !== "");
}

declare global {
    interface Window {
        dataLayer?: unknown[];
        gtag?: GoogleTagFunction;
        fbq?: (...args: unknown[]) => void;
    }
}

export function trackEvent(name: string, parameters?: EventParameters) {
    if (typeof window === "undefined" || !hasAnalyticsConsent()) return false;

    const eventParameters = withAnalyticsContext(parameters);

    if (window.gtag) {
        window.gtag("event", name, eventParameters);
    } else if (window.dataLayer) {
        window.dataLayer.push({ event: name, ...eventParameters });
    }

    if (name === "generate_lead") {
        window.fbq?.("track", "Lead", eventParameters);
    } else if (name === "contact") {
        window.fbq?.("track", "Contact", eventParameters);
    }

    return true;
}
