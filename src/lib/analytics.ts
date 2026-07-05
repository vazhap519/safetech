"use client";

type EventParameters = Record<string, string | number | boolean | undefined>;

declare global {
    interface Window {
        dataLayer?: unknown[];
        gtag?: (command: "event", name: string, parameters?: EventParameters) => void;
    }
}

export function trackEvent(name: string, parameters?: EventParameters) {
    window.gtag?.("event", name, parameters);
}
