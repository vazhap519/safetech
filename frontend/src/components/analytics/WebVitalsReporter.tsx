"use client";

import { useReportWebVitals } from "next/web-vitals";
import { hasAnalyticsConsent } from "@/lib/consent";

const TRACKED = new Set(["LCP", "INP", "CLS", "FCP", "TTFB"]);

export default function WebVitalsReporter() {
    useReportWebVitals((metric) => {
        if (!TRACKED.has(metric.name) || !hasAnalyticsConsent()) return;
        const body = JSON.stringify({
            event_type: "web_vital",
            page_path: window.location.pathname,
            locale: document.documentElement.lang,
            meta: {
                metric: metric.name,
                value: String(Math.round(metric.value * (metric.name === "CLS" ? 1000 : 1)) / (metric.name === "CLS" ? 1000 : 1)),
                rating: metric.rating,
                delta: String(Math.round(metric.delta * 1000) / 1000),
                navigation: metric.navigationType,
            },
        });
        navigator.sendBeacon?.("/api/analytics/events", new Blob([body], { type: "application/json" }));
    });

    return null;
}
