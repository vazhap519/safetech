"use client";

import { useEffect, useState, type RefObject } from "react";
import Script from "next/script";
import { usePathname } from "next/navigation";

import { captureCampaignAttribution, withAnalyticsContext } from "@/lib/analytics";
import { hasAnalyticsConsent } from "@/lib/consent";

declare global {
    interface Window {
        _fbq?: (...args: unknown[]) => void;
        safetechGoogleAnalyticsId?: string;
    }
}

function validId(value: string | undefined, pattern: RegExp) {
    const normalized = value?.trim() ?? "";
    return pattern.test(normalized) ? normalized : "";
}

function MarketingRouteTracker({
    googleAnalyticsId, googleTagManagerId, metaPixelId, googleReady, metaReady,
    previousGooglePageRef, previousMetaPageRef,
}: {
    googleAnalyticsId: string;
    googleTagManagerId: string;
    metaPixelId: string;
    googleReady: boolean;
    metaReady: boolean;
    previousGooglePageRef: RefObject<string | null>;
    previousMetaPageRef: RefObject<string | null>;
}) {
    const pathname = usePathname() || "/";

    useEffect(() => {
        if (!hasAnalyticsConsent()) return;
        const location = window.location.href;
        const pageViewParameters = withAnalyticsContext({
            page_location: location,
            page_path: pathname,
            page_title: document.title,
            page_referrer: previousGooglePageRef.current ?? document.referrer,
        });

        // Each provider becomes ready independently, including Meta-only sites.
        if (googleReady && previousGooglePageRef.current !== location) {
            if (googleAnalyticsId && window.gtag) {
                window.gtag("event", "page_view", pageViewParameters);
                previousGooglePageRef.current = location;
            } else if (googleTagManagerId) {
                window.dataLayer = window.dataLayer || [];
                window.dataLayer.push({ event: "virtual_page_view", ...pageViewParameters });
                previousGooglePageRef.current = location;
            }
        }
        if (metaReady && metaPixelId && window.fbq && previousMetaPageRef.current !== location) {
            window.fbq("track", "PageView");
            previousMetaPageRef.current = location;
        }
    }, [googleAnalyticsId, googleTagManagerId, metaPixelId, googleReady, metaReady, pathname, previousGooglePageRef, previousMetaPageRef]);
    return null;
}

export default function MarketingScriptsClient({
    googleTagManagerId, googleAnalyticsId, metaPixelId,
    previousGooglePageRef, previousMetaPageRef,
}: {
    googleTagManagerId?: string;
    googleAnalyticsId?: string;
    metaPixelId?: string;
    previousGooglePageRef: RefObject<string | null>;
    previousMetaPageRef: RefObject<string | null>;
}) {
    const gaId = validId(googleAnalyticsId, /^G-[A-Z0-9]+$/i);
    // One Google measurement owner: direct GA4 takes precedence over GTM.
    const gtmId = gaId ? "" : validId(googleTagManagerId, /^GTM-[A-Z0-9]+$/i);
    const pixelId = validId(metaPixelId, /^\d{5,30}$/);
    const [googleReady, setGoogleReady] = useState(false);
    const [metaReady, setMetaReady] = useState(false);

    useEffect(() => { captureCampaignAttribution(); }, []);

    function googleInitialized() {
        if (!hasAnalyticsConsent()) return;
        window.gtag?.("consent", "update", {
            ad_storage: "granted", ad_user_data: "granted",
            ad_personalization: "granted", analytics_storage: "granted",
        });
        if (gaId) {
            window.safetechGoogleAnalyticsId = gaId;
            (window as unknown as Record<string, unknown>)[`ga-disable-${gaId}`] = false;
        }
        setGoogleReady(true);
    }

    return (
        <>
            {gaId || gtmId ? (
                <Script id="google-marketing-init" strategy="afterInteractive" onReady={googleInitialized}>
                    {`window.dataLayer=window.dataLayer||[];window.gtag=window.gtag||function(){window.dataLayer.push(arguments)};${gaId
                        ? `gtag('js',new Date());gtag('config','${gaId}',{send_page_view:false});`
                        : `(function(w,d,s,l,i){w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${gtmId}');`}`}
                </Script>
            ) : null}
            {gaId ? (
                <Script src={`https://www.googletagmanager.com/gtag/js?id=${gaId}`} strategy="afterInteractive" />
            ) : null}
            {pixelId ? (
                <Script id="meta-pixel" strategy="lazyOnload" onReady={() => {
                    if (!hasAnalyticsConsent()) return;
                    window.fbq?.("consent", "grant");
                    setMetaReady(true);
                }}>
                    {`!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','${pixelId}');`}
                </Script>
            ) : null}
            <MarketingRouteTracker googleAnalyticsId={gaId} googleTagManagerId={gtmId}
                metaPixelId={pixelId} googleReady={googleReady} metaReady={metaReady}
                previousGooglePageRef={previousGooglePageRef} previousMetaPageRef={previousMetaPageRef} />
        </>
    );
}
