"use client";

import { trackEvent } from "@/lib/analytics";

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

export function getVisitorId() {
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

    const { pathname, search } = window.location;

    return `${pathname}${search}`;
}

export function extractServiceSlugFromPath(pagePath?: string | null) {
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

export function sendAnalyticsEvent(
    payload: AnalyticsPayload,
    options?: { immediate?: boolean },
) {
    if (typeof window === "undefined") {
        return;
    }

    const body = buildRequestBody(payload);
    const url = "/api/analytics/events";

    if (options?.immediate && typeof navigator.sendBeacon === "function") {
        const blob = new Blob([body], { type: "application/json" });

        navigator.sendBeacon(url, blob);

        return;
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
}

export function trackServiceView(serviceSlug: string, pagePath?: string | null) {
    sendAnalyticsEvent({
        eventType: "service_view",
        serviceSlug,
        pagePath,
    });
}

export function trackWhatsAppClick(pagePath?: string | null) {
    trackEvent("contact", { method: "whatsapp" });

    sendAnalyticsEvent(
        {
            eventType: "whatsapp_click",
            pagePath,
        },
        { immediate: true },
    );
}
