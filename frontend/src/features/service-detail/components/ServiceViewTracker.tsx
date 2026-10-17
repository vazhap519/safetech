"use client";

import { useEffect, useRef, useSyncExternalStore } from "react";
import { usePathname } from "next/navigation";

import { trackInternalEvent, trackServiceView } from "@/lib/analytics-events";
import {
    readAnalyticsConsent,
    subscribeToAnalyticsConsent,
} from "@/lib/consent";

type ServiceViewTrackerProps = {
    serviceSlug?: string;
};

export default function ServiceViewTracker({
    serviceSlug,
}: ServiceViewTrackerProps) {
    const pathname = usePathname();
    const consent = useSyncExternalStore(
        subscribeToAnalyticsConsent,
        readAnalyticsConsent,
        () => "unknown",
    );
    const lastTrackedKey = useRef<string | null>(null);
    const pagePath = pathname;

    useEffect(() => {
        const trackingKey = `${serviceSlug}:${pagePath}`;

        if (lastTrackedKey.current === trackingKey) {
            return;
        }

        const tracked = serviceSlug
            ? trackServiceView(serviceSlug, pagePath)
            : trackInternalEvent("service_view", { pagePath });

        if (tracked) {
            lastTrackedKey.current = trackingKey;
        }
    }, [consent, pagePath, serviceSlug]);

    return null;
}
