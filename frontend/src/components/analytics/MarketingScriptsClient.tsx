"use client";

import { useEffect, useRef, useState } from "react";
import Script from "next/script";
import { usePathname } from "next/navigation";

import {
    captureCampaignAttribution,
    withAnalyticsContext,
} from "@/lib/analytics";

declare global {
    interface Window {
        dataLayer?: unknown[];
        fbq?: (...args: unknown[]) => void;
        _fbq?: (...args: unknown[]) => void;
    }
}

function validId(value: string | undefined, pattern: RegExp) {
    const normalized = value?.trim() ?? "";
    return pattern.test(normalized) ? normalized : "";
}

function MarketingRouteTracker({
    googleTagManagerId,
    googleAnalyticsId,
    metaPixelId,
}: {
    googleTagManagerId: string;
    googleAnalyticsId: string;
    metaPixelId: string;
}) {
    const pathname = usePathname() || "/";
    const previousPath = useRef<string | null>(null);

    useEffect(() => {
        // Capture the first page after consent as well as client-side navigation.
        if (previousPath.current === pathname) return;
        previousPath.current = pathname;
        const pageViewParameters = withAnalyticsContext({
            page_location: window.location.href,
            page_path: pathname,
            page_title: document.title,
        });

        if (googleAnalyticsId && window.gtag) {
            window.gtag("event", "page_view", pageViewParameters);
        } else if (googleTagManagerId) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: "virtual_page_view",
                ...pageViewParameters,
            });
        }

        if (metaPixelId && window.fbq) {
            window.fbq("track", "PageView");
        }
    }, [googleAnalyticsId, googleTagManagerId, metaPixelId, pathname]);

    return null;
}

export default function MarketingScriptsClient({
    googleTagManagerId,
    googleAnalyticsId,
    metaPixelId,
}: {
    googleTagManagerId?: string;
    googleAnalyticsId?: string;
    metaPixelId?: string;
}) {
    const gtmId = validId(googleTagManagerId, /^GTM-[A-Z0-9]+$/i);
    const gaId = validId(googleAnalyticsId, /^G-[A-Z0-9]+$/i);
    const pixelId = validId(metaPixelId, /^\d{5,30}$/);
    // Prefer direct GA4 and do not load a second GTM measurement path.
    const activeGtmId = gaId ? "" : gtmId;
    const [tagReady, setTagReady] = useState(false);

    useEffect(() => {
        captureCampaignAttribution();
    }, []);

    return (
        <>
            {activeGtmId ? (
                <Script id="google-tag-manager" strategy="afterInteractive" onReady={() => setTagReady(true)}>
                    {`gtag('consent','update',{ad_storage:'granted',ad_user_data:'granted',ad_personalization:'granted',analytics_storage:'granted'});(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${activeGtmId}');`}
                </Script>
            ) : null}

            {gaId ? (
                <Script id="google-analytics-consent" strategy="afterInteractive" onReady={() => setTagReady(true)}>
                    {`window.dataLayer=window.dataLayer||[];window.gtag=window.gtag||function(){dataLayer.push(arguments)};gtag('consent','update',{ad_storage:'granted',ad_user_data:'granted',ad_personalization:'granted',analytics_storage:'granted'});gtag('config','${gaId}',{anonymize_ip:true,send_page_view:false});`}
                </Script>
            ) : null}

            {pixelId ? (
                <Script id="meta-pixel" strategy="lazyOnload">
                    {`!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','${pixelId}');fbq('track','PageView');`}
                </Script>
            ) : null}

            {tagReady ? (
                <MarketingRouteTracker
                    googleAnalyticsId={gaId}
                    googleTagManagerId={activeGtmId}
                    metaPixelId={pixelId}
                />
            ) : null}
        </>
    );
}
