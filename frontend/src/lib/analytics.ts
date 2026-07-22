"use client";

type EventParameters = Record<string, string | number | boolean | undefined>;

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

declare global {
    interface Window {
        dataLayer?: unknown[];
        gtag?: GoogleTagFunction;
        fbq?: (...args: unknown[]) => void;
    }
}

export function trackEvent(name: string, parameters?: EventParameters) {
    if (window.gtag) {
        window.gtag("event", name, parameters);
    } else if (window.dataLayer) {
        window.dataLayer.push({ event: name, ...parameters });
    }

    if (name === "generate_lead") {
        window.fbq?.("track", "Lead", parameters);
    }
}
