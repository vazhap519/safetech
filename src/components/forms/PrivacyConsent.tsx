"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";

export default function PrivacyConsent() {
    const { t } = useLocalization();

    return (
        <label className="flex items-start gap-3 text-sm leading-relaxed text-on-surface-variant">
            <input
                className="mt-1 size-4 shrink-0 accent-primary-container"
                name="privacy"
                required
                type="checkbox"
                value="accepted"
            />
            <span>
                {t("forms.privacy", {
                    ka: "ვეთანხმები ჩემი საკონტაქტო მონაცემების დამუშავებას მოთხოვნაზე პასუხის გასაცემად.",
                    en: "I agree to the processing of my contact details for responding to my request.",
                    ru: "Я согласен(на) на обработку моих контактных данных для ответа на мой запрос.",
                })}
            </span>
        </label>
    );
}
