"use client";

import { useEffect, useRef, useSyncExternalStore } from "react";
import Script from "next/script";
import { usePathname } from "next/navigation";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import LocalizedLink from "@/components/ui/LocalizedLink";
import {
    type AnalyticsConsent,
    readAnalyticsConsent,
    saveAnalyticsConsent,
    subscribeToAnalyticsConsent,
} from "@/lib/consent";

declare global {
    interface Window {
        dataLayer?: unknown[];
        fbq?: (...args: unknown[]) => void;
        _fbq?: (...args: unknown[]) => void;
    }
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
        if (previousPath.current === null) {
            previousPath.current = pathname;
            return;
        }

        if (previousPath.current === pathname) return;
        previousPath.current = pathname;

        if (googleTagManagerId) {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({
                event: "virtual_page_view",
                page_location: window.location.href,
                page_path: pathname,
                page_title: document.title,
            });
        } else if (googleAnalyticsId && window.gtag) {
            window.gtag("event", "page_view", {
                page_location: window.location.href,
                page_path: pathname,
                page_title: document.title,
            });
        }

        if (metaPixelId && window.fbq) {
            window.fbq("track", "PageView");
        }
    }, [googleAnalyticsId, googleTagManagerId, metaPixelId, pathname]);

    return null;
}

function GoogleConsentSync({
    consent,
    enabled,
}: {
    consent: AnalyticsConsent;
    enabled: boolean;
}) {
    useEffect(() => {
        if (!enabled || !window.gtag) return;

        const state = consent === "accepted" ? "granted" : "denied";
        window.gtag("consent", "update", {
            ad_storage: state,
            ad_user_data: state,
            ad_personalization: state,
            analytics_storage: state,
        });
    }, [consent, enabled]);

    return null;
}

function validId(value: string | undefined, pattern: RegExp) {
    const normalized = value?.trim() ?? "";
    return pattern.test(normalized) ? normalized : "";
}

export default function MarketingPixels({
    enabled,
    googleTagManagerId,
    googleAnalyticsId,
    metaPixelId,
}: {
    enabled: boolean;
    googleTagManagerId?: string;
    googleAnalyticsId?: string;
    metaPixelId?: string;
}) {
    const { t } = useLocalization();
    const consent = useSyncExternalStore<AnalyticsConsent>(
        subscribeToAnalyticsConsent,
        readAnalyticsConsent,
        (): AnalyticsConsent => "unknown",
    );
    const gtmId = validId(googleTagManagerId, /^GTM-[A-Z0-9]+$/i);
    const gaId = validId(googleAnalyticsId, /^G-[A-Z0-9]+$/i);
    const pixelId = validId(metaPixelId, /^\d{5,30}$/);
    const hasIntegration = Boolean(gtmId || gaId || pixelId);
    const hasGoogleIntegration = Boolean(gtmId || gaId);

    if (!enabled) return null;

    return (
        <>
            <GoogleConsentSync
                consent={consent}
                enabled={hasGoogleIntegration}
            />
            {consent === "accepted" && hasIntegration ? (
                <>
                    {gtmId ? (
                        <Script id="google-tag-manager" strategy="afterInteractive">
                            {`gtag('consent','update',{ad_storage:'granted',ad_user_data:'granted',ad_personalization:'granted',analytics_storage:'granted'});(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','${gtmId}');`}
                        </Script>
                    ) : gaId ? (
                        <>
                            <Script
                                src={`https://www.googletagmanager.com/gtag/js?id=${gaId}`}
                                strategy="afterInteractive"
                            />
                            <Script id="google-analytics" strategy="afterInteractive">
                                {`gtag('consent','update',{ad_storage:'granted',ad_user_data:'granted',ad_personalization:'granted',analytics_storage:'granted'});gtag('js',new Date());gtag('config','${gaId}',{anonymize_ip:true});`}
                            </Script>
                        </>
                    ) : null}

                    {pixelId ? (
                        <Script id="meta-pixel" strategy="afterInteractive">
                            {`!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');fbq('init','${pixelId}');fbq('track','PageView');`}
                        </Script>
                    ) : null}

                    <MarketingRouteTracker
                        googleAnalyticsId={gaId}
                        googleTagManagerId={gtmId}
                        metaPixelId={pixelId}
                    />
                </>
            ) : null}

            {consent === "unknown" ? (
                <section
                    aria-label={t("consent.ariaLabel", {
                        ka: "კონფიდენციალურობის არჩევანი",
                        en: "Privacy choice",
                        ru: "Настройки конфиденциальности",
                    })}
                    className="fixed inset-x-4 bottom-4 z-[80] mx-auto max-w-4xl rounded-lg border border-outline-variant/40 bg-surface-container-high p-4 shadow-2xl sm:flex sm:items-center sm:gap-5"
                >
                    <p className="min-w-0 flex-1 text-sm leading-6 text-on-surface-variant">
                        {t("consent.message", {
                            ka: "ვიყენებთ ანალიტიკისა და სარეკლამო ტექნოლოგიებს საიტის მუშაობისა და კამპანიების გასაზომად.",
                            en: "We use analytics and advertising technologies to measure site performance and campaigns.",
                            ru: "Мы используем аналитику и рекламные технологии для оценки работы сайта и кампаний.",
                        })}{" "}
                        <LocalizedLink className="text-secondary underline" href="/privacy">
                            {t("consent.learnMore", {
                                ka: "დეტალურად",
                                en: "Learn more",
                                ru: "Подробнее",
                            })}
                        </LocalizedLink>
                    </p>
                    <div className="mt-4 flex shrink-0 gap-3 sm:mt-0">
                        <button
                            className="min-h-11 rounded-lg border border-outline-variant/50 px-4 text-sm font-medium text-on-surface"
                            onClick={() => saveAnalyticsConsent("rejected")}
                            type="button"
                        >
                            {t("consent.reject", { ka: "უარყოფა", en: "Reject", ru: "Отклонить" })}
                        </button>
                        <button
                            className="min-h-11 rounded-lg bg-primary-container px-4 text-sm font-semibold text-on-primary-container"
                            onClick={() => saveAnalyticsConsent("accepted")}
                            type="button"
                        >
                            {t("consent.accept", { ka: "დათანხმება", en: "Accept", ru: "Принять" })}
                        </button>
                    </div>
                </section>
            ) : null}
        </>
    );
}
