"use client";

import { trackEvent } from "@/lib/analytics";
import { hasAnalyticsConsent } from "@/lib/consent";

type AnalyticsEventType = "service_view" | "whatsapp_click";

type AnalyticsPayload = {
    eventType: AnalyticsEventType;
    serviceSlug?: string | null;
    pagePath?: string | null;
    locale?: string | null;
    meta?: Record<string, string | number | boolean>;
};

const VISITOR_STORAGE_KEY = "safetech_visitor_id";

function generateVisitorId() {
    if (typeof crypto !== "undefined" && typeof crypto.randomUUID === "function") {
        return crypto.randomUUID();
    }

    return `visitor-${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
}

function getVisitorId() {
    if (typeof window === "undefined") {
        return null;
    }

    try {
        const existing = window.localStorage.getItem(VISITOR_STORAGE_KEY);

        if (existing) {
            return existing;
        }

        const visitorId = generateVisitorId();
        window.localStorage.setItem(VISITOR_STORAGE_KEY, visitorId);

        return visitorId;
    } catch {
        return generateVisitorId();
    }
}

export function getCurrentPagePath() {
    if (typeof window === "undefined") {
        return null;
    }

    return window.location.pathname;
}

function extractServiceSlugFromPath(pagePath?: string | null) {
    if (!pagePath) {
        return null;
    }

    const match = pagePath.match(/^\/services\/([^/?#]+)/);

    return match?.[1] ?? null;
}

function buildRequestBody(payload: AnalyticsPayload) {
    const pagePath = payload.pagePath ?? getCurrentPagePath();
    const serviceSlug = payload.serviceSlug ?? extractServiceSlugFromPath(pagePath);

    return JSON.stringify({
        event_type: payload.eventType,
        service_slug: serviceSlug,
        page_path: pagePath,
        locale:
            payload.locale ||
            (typeof document !== "undefined"
                ? document.documentElement.lang || undefined
                : undefined),
        visitor_id: getVisitorId(),
        meta: payload.meta,
    });
}

function sendAnalyticsEvent(
    payload: AnalyticsPayload,
    options?: { immediate?: boolean },
) {
    if (typeof window === "undefined" || !hasAnalyticsConsent()) {
        return false;
    }

    const body = buildRequestBody(payload);
    const url = "/api/analytics/events";

    if (options?.immediate && typeof navigator.sendBeacon === "function") {
        const blob = new Blob([body], { type: "application/json" });

        navigator.sendBeacon(url, blob);

        return true;
    }

    void fetch(url, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            Accept: "application/json",
        },
        body,
        keepalive: options?.immediate ?? false,
    }).catch(() => undefined);

    return true;
}

export function trackServiceView(serviceSlug: string, pagePath?: string | null) {
    return sendAnalyticsEvent({
        eventType: "service_view",
        serviceSlug,
        pagePath,
    });
}

export function trackWhatsAppClick(pagePath?: string | null) {
    trackEvent("contact", { method: "whatsapp" });

    return sendAnalyticsEvent(
        {
            eventType: "whatsapp_click",
            pagePath,
        },
        { immediate: true },
    );
}
