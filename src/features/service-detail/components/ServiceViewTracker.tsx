"use client";

import { useEffect, useRef } from "react";
import { usePathname, useSearchParams } from "next/navigation";

import { trackServiceView } from "@/lib/analytics-events";

type ServiceViewTrackerProps = {
    serviceSlug: string;
};

export default function ServiceViewTracker({
    serviceSlug,
}: ServiceViewTrackerProps) {
    const pathname = usePathname();
    const searchParams = useSearchParams();
    const lastTrackedKey = useRef<string | null>(null);
    const pagePath = `${pathname}${searchParams.toString() ? `?${searchParams.toString()}` : ""}`;

    useEffect(() => {
        const trackingKey = `${serviceSlug}:${pagePath}`;

        if (lastTrackedKey.current === trackingKey) {
            return;
        }

        lastTrackedKey.current = trackingKey;
        trackServiceView(serviceSlug, pagePath);
    }, [pagePath, serviceSlug]);

    return null;
}
