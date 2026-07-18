"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";

type PrivacyConsentProps = {
    label?: string | null;
};

export default function PrivacyConsent({ label }: PrivacyConsentProps) {
    const { t } = useLocalization();
    const consentLabel = label ?? t("forms.privacy", null);

    if (!consentLabel) return null;

    return (
        <label className="flex items-start gap-3 text-sm leading-relaxed text-on-surface-variant">
            <input
                className="mt-1 size-4 shrink-0 accent-primary-container"
                name="privacy"
                required
                type="checkbox"
                value="accepted"
            />
            <span>{consentLabel}</span>
        </label>
    );
}
