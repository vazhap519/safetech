"use client";

import dynamic from "next/dynamic";
import { useSyncExternalStore } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import LocalizedLink from "@/components/ui/LocalizedLink";
import {
    type AnalyticsConsent,
    readAnalyticsConsent,
    saveAnalyticsConsent,
    subscribeToAnalyticsConsent,
} from "@/lib/consent";

const MarketingScriptsClient = dynamic(
    () => import("@/components/analytics/MarketingScriptsClient"),
    {
        ssr: false,
        loading: () => null,
    },
);

function validId(value: string | undefined, pattern: RegExp) {
    const normalized = value?.trim() ?? "";
    return pattern.test(normalized) ? normalized : "";
}

export default function AnalyticsConsentClient({
    googleTagManagerId,
    googleAnalyticsId,
    metaPixelId,
    initialConsent,
}: {
    googleTagManagerId?: string;
    googleAnalyticsId?: string;
    metaPixelId?: string;
    initialConsent: AnalyticsConsent;
}) {
    const { t } = useLocalization();
    const consent = useSyncExternalStore<AnalyticsConsent>(
        subscribeToAnalyticsConsent,
        readAnalyticsConsent,
        () => initialConsent,
    );
    const gtmId = validId(googleTagManagerId, /^GTM-[A-Z0-9]+$/i);
    const gaId = validId(googleAnalyticsId, /^G-[A-Z0-9]+$/i);
    const pixelId = validId(metaPixelId, /^\d{5,30}$/);
    const hasIntegration = Boolean(gtmId || gaId || pixelId);

    return (
        <>
            {consent === "accepted" && hasIntegration ? (
                <MarketingScriptsClient
                    googleAnalyticsId={gaId}
                    googleTagManagerId={gtmId}
                    metaPixelId={pixelId}
                />
            ) : null}

            {consent === "unknown" ? (
                <section
                    aria-label={t("consent.ariaLabel", {
                        ka: "კონფიდენციალურობის არჩევანი",
                        en: "Privacy choice",
                        ru: "Настройки конфиденциальности",
                    })}
                    className="marketing-consent-banner fixed inset-x-4 bottom-4 z-[80] mx-auto flex max-w-4xl flex-col justify-center overflow-y-auto rounded-lg border border-outline-variant/40 bg-surface-container-high p-4 shadow-2xl sm:flex-row sm:items-center sm:gap-5"
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
                            className="min-h-11 flex-1 rounded-lg border border-outline-variant/50 px-4 text-sm font-medium text-on-surface sm:flex-none"
                            onClick={() => saveAnalyticsConsent("rejected")}
                            type="button"
                        >
                            {t("consent.reject", {
                                ka: "უარყოფა",
                                en: "Reject",
                                ru: "Отклонить",
                            })}
                        </button>
                        <button
                            className="min-h-11 flex-1 rounded-lg bg-primary-container px-4 text-sm font-semibold text-on-primary-container sm:flex-none"
                            onClick={() => saveAnalyticsConsent("accepted")}
                            type="button"
                        >
                            {t("consent.accept", {
                                ka: "დათანხმება",
                                en: "Accept",
                                ru: "Принять",
                            })}
                        </button>
                    </div>
                </section>
            ) : null}
        </>
    );
}
