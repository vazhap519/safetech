"use client";

import { ANALYTICS_CONSENT_COOKIE } from "@/lib/consent-config";

export type AnalyticsConsent = "unknown" | "accepted" | "rejected";

const ANALYTICS_CONSENT_KEY = ANALYTICS_CONSENT_COOKIE;
const ANALYTICS_CONSENT_EVENT = "safetech:marketing-consent";
const COOKIE_MAX_AGE = 60 * 60 * 24 * 180;

function readConsentCookie(): AnalyticsConsent | null {
    if (typeof document === "undefined") return null;

    const prefix = `${ANALYTICS_CONSENT_COOKIE}=`;
    const value = document.cookie
        .split("; ")
        .find((item) => item.startsWith(prefix))
        ?.slice(prefix.length);

    return value === "accepted" || value === "rejected" ? value : null;
}

function writeConsentCookie(consent: "accepted" | "rejected") {
    const secure = window.location.protocol === "https:" ? "; Secure" : "";
    document.cookie = `${ANALYTICS_CONSENT_COOKIE}=${consent}; Path=/; Max-Age=${COOKIE_MAX_AGE}; SameSite=Lax${secure}`;
}

export function readAnalyticsConsent(): AnalyticsConsent {
    if (typeof window === "undefined") return "unknown";

    try {
        const stored = window.localStorage.getItem(ANALYTICS_CONSENT_KEY);

        if (stored === "accepted" || stored === "rejected") return stored;
    } catch {
        // Privacy modes can disable localStorage while cookies remain available.
    }

    return readConsentCookie() ?? "unknown";
}

export function subscribeToAnalyticsConsent(onStoreChange: () => void) {
    if (typeof window === "undefined") return () => undefined;

    const handleStorage = (event: StorageEvent) => {
        if (event.key === ANALYTICS_CONSENT_KEY) onStoreChange();
    };

    window.addEventListener("storage", handleStorage);
    window.addEventListener(ANALYTICS_CONSENT_EVENT, onStoreChange);

    return () => {
        window.removeEventListener("storage", handleStorage);
        window.removeEventListener(ANALYTICS_CONSENT_EVENT, onStoreChange);
    };
}

export function saveAnalyticsConsent(
    consent: Exclude<AnalyticsConsent, "unknown">,
) {
    try {
        window.localStorage.setItem(ANALYTICS_CONSENT_KEY, consent);
    } catch {
        // The consent cookie remains the durable fallback.
    }
    writeConsentCookie(consent);
    window.dispatchEvent(new Event(ANALYTICS_CONSENT_EVENT));
}

export function hasAnalyticsConsent() {
    return readAnalyticsConsent() === "accepted";
}

export function resetAnalyticsConsent() {
    try {
        window.localStorage.removeItem(ANALYTICS_CONSENT_KEY);
    } catch {
        // Keep reset usable when localStorage is unavailable.
    }
    const secure = window.location.protocol === "https:" ? "; Secure" : "";
    document.cookie = `${ANALYTICS_CONSENT_COOKIE}=; Path=/; Max-Age=0; SameSite=Lax${secure}`;
    window.dispatchEvent(new Event(ANALYTICS_CONSENT_EVENT));
}
