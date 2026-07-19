"use client";

type EventParameters = Record<string, string | number | boolean | undefined>;

declare global {
    interface Window {
        dataLayer?: unknown[];
        gtag?: (command: "event", name: string, parameters?: EventParameters) => void;
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
