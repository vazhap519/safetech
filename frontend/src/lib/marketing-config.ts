type IntegrationIds = {
    googleAnalyticsId?: string;
    googleTagManagerId?: string;
    metaPixelId?: string;
};

function validId(value: string | undefined, pattern: RegExp) {
    const normalized = value?.trim() || "";
    return pattern.test(normalized) ? normalized : "";
}

export function resolveMarketingIds(configured: IntegrationIds) {
    const ga = validId(configured.googleAnalyticsId, /^G-[A-Z0-9]+$/i);
    const gtm = validId(configured.googleTagManagerId, /^GTM-[A-Z0-9]+$/i);
    const adminConfigured = Boolean(ga || gtm);
    const googleAnalyticsId = ga || (!adminConfigured ? validId(process.env.NEXT_PUBLIC_GA_ID, /^G-[A-Z0-9]+$/i) : "");
    const googleTagManagerId = googleAnalyticsId ? "" : gtm || (!adminConfigured ? validId(process.env.NEXT_PUBLIC_GTM_ID, /^GTM-[A-Z0-9]+$/i) : "");
    const metaPixelId = validId(configured.metaPixelId || process.env.NEXT_PUBLIC_META_PIXEL_ID, /^\d{5,30}$/);
    return { googleAnalyticsId, googleTagManagerId, metaPixelId };
}
