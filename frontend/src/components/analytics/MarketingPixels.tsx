import dynamic from "next/dynamic";

import type { AnalyticsConsent } from "@/lib/consent";

const MarketingPixelsClient = dynamic(
    () => import("@/components/analytics/MarketingPixelsClient"),
);

export default function MarketingPixels({
    enabled,
    googleTagManagerId,
    googleAnalyticsId,
    metaPixelId,
    initialConsent,
}: {
    enabled: boolean;
    googleTagManagerId?: string;
    googleAnalyticsId?: string;
    metaPixelId?: string;
    initialConsent: AnalyticsConsent;
}) {
    if (!enabled) return null;

    return (
        <MarketingPixelsClient
            googleAnalyticsId={googleAnalyticsId}
            googleTagManagerId={googleTagManagerId}
            initialConsent={initialConsent}
            metaPixelId={metaPixelId}
        />
    );
}
