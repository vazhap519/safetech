import AnalyticsConsentClient from "@/components/analytics/AnalyticsConsentClient";
import type { AnalyticsConsent } from "@/lib/consent";

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
        <AnalyticsConsentClient
            googleAnalyticsId={googleAnalyticsId}
            googleTagManagerId={googleTagManagerId}
            initialConsent={initialConsent}
            metaPixelId={metaPixelId}
        />
    );
}
