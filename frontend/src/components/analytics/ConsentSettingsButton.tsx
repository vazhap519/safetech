"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { resetAnalyticsConsent } from "@/lib/consent";

export default function ConsentSettingsButton({ enabled }: { enabled: boolean }) {
    const { t } = useLocalization();

    if (!enabled) return null;

    return (
        <button
            className="text-sm text-on-surface-variant opacity-80 underline-offset-4 hover:underline focus-visible:rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-primary"
            onClick={resetAnalyticsConsent}
            type="button"
        >
            {t("consent.settings", {
                ka: "კონფიდენციალურობის პარამეტრები",
                en: "Privacy settings",
                ru: "Настройки конфиденциальности",
            })}
        </button>
    );
}
