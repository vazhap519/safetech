"use client";

export type AnalyticsConsent = "unknown" | "accepted" | "rejected";

const ANALYTICS_CONSENT_KEY = "safetech_marketing_consent";
const ANALYTICS_CONSENT_EVENT = "safetech:marketing-consent";

export function readAnalyticsConsent(): AnalyticsConsent {
    if (typeof window === "undefined") return "unknown";

    const stored = window.localStorage.getItem(ANALYTICS_CONSENT_KEY);

    return stored === "accepted" || stored === "rejected" ? stored : "unknown";
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
    window.localStorage.setItem(ANALYTICS_CONSENT_KEY, consent);
    window.dispatchEvent(new Event(ANALYTICS_CONSENT_EVENT));
}

export function hasAnalyticsConsent() {
    return readAnalyticsConsent() === "accepted";
}

export function resetAnalyticsConsent() {
    window.localStorage.removeItem(ANALYTICS_CONSENT_KEY);
    window.dispatchEvent(new Event(ANALYTICS_CONSENT_EVENT));
}
