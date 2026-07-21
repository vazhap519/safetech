"use client";

import { useId } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import LocalizedLink from "@/components/ui/LocalizedLink";

type PrivacyConsentProps = {
    label?: string | null;
};

export default function PrivacyConsent({ label }: PrivacyConsentProps) {
    const inputId = useId();
    const { t } = useLocalization();
    const consentLabel = label ?? t("forms.privacy", null);
    const policyLabel = t("forms.privacyPolicyLink", {
        ka: "პოლიტიკის ნახვა",
        en: "View policy",
        ru: "Открыть политику",
    });

    if (!consentLabel) return null;

    return (
        <div className="flex items-start gap-3 text-sm leading-relaxed text-on-surface-variant">
            <input
                className="mt-1 size-4 shrink-0 accent-primary-container"
                id={inputId}
                name="privacy"
                required
                type="checkbox"
                value="1"
            />
            <span>
                <label htmlFor={inputId}>{consentLabel}</label>{" "}
                <LocalizedLink
                    className="text-secondary underline underline-offset-2"
                    href="/privacy"
                    target="_blank"
                >
                    {policyLabel}
                </LocalizedLink>
            </span>
        </div>
    );
}
