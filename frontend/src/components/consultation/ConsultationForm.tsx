"use client";

import { CONSULTATION_POPOVER_ID } from "@/components/consultation/constants";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLeadForm } from "@/hooks/useLeadForm";

export default function ConsultationForm() {
    const { status, message, submit } = useLeadForm("consultation-popup");
    const { t } = useLocalization();

    return (
        <form className="space-y-unit-md" onSubmit={submit}>
            <input
                aria-hidden="true"
                autoComplete="off"
                className="sr-only"
                name="website"
                tabIndex={-1}
                type="text"
            />
            <div className="grid gap-unit-md sm:grid-cols-2">
                <label className="space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>
                        {t("forms.firstName", {
                            ka: "სახელი",
                            en: "First name",
                            ru: "Имя",
                        })}
                    </span>
                    <input
                        autoFocus
                        autoComplete="given-name"
                        className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        name="firstName"
                        required
                        type="text"
                    />
                </label>
                <label className="space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>
                        {t("forms.lastName", {
                            ka: "გვარი",
                            en: "Last name",
                            ru: "Фамилия",
                        })}
                    </span>
                    <input
                        autoComplete="family-name"
                        className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        name="lastName"
                        required
                        type="text"
                    />
                </label>
            </div>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>
                    {t("forms.phone", {
                        ka: "ტელეფონი",
                        en: "Phone",
                        ru: "Телефон",
                    })}
                </span>
                <input
                    autoComplete="tel"
                    className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                    inputMode="tel"
                    name="phone"
                    required
                    type="tel"
                />
            </label>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>
                    {t("forms.email", {
                        ka: "ელფოსტა",
                        en: "Email",
                        ru: "Email",
                    })}
                </span>
                <input
                    autoComplete="email"
                    className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                    name="email"
                    type="email"
                />
            </label>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>
                    {t("forms.details", {
                        ka: "დამატებითი ინფორმაცია",
                        en: "Additional details",
                        ru: "Дополнительная информация",
                    })}
                </span>
                <textarea
                    className="min-h-32 w-full resize-y rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                    name="details"
                    rows={5}
                />
            </label>
            <PrivacyConsent />
            <p
                aria-live="polite"
                className={status === "error" ? "text-error" : "text-success"}
                role="status"
            >
                {message}
            </p>
            <div className="flex flex-col-reverse gap-unit-sm pt-unit-sm sm:flex-row sm:justify-end">
                <button
                    className="rounded-xl border border-outline-variant px-6 py-3 font-medium text-on-surface transition-colors hover:bg-surface-container-high"
                    popoverTarget={CONSULTATION_POPOVER_ID}
                    popoverTargetAction="hide"
                    type="button"
                >
                    {t("common.cancel", {
                        ka: "გაუქმება",
                        en: "Cancel",
                        ru: "Отмена",
                    })}
                </button>
                <button
                    className="rounded-xl bg-primary-container px-6 py-3 font-medium text-on-primary-container shadow-lg shadow-blue-500/20 transition hover:brightness-110 disabled:cursor-wait disabled:opacity-60"
                    disabled={status === "submitting"}
                    type="submit"
                >
                    {status === "submitting"
                        ? t("forms.submitting", {
                              ka: "იგზავნება...",
                              en: "Sending...",
                              ru: "Отправка...",
                          })
                        : t("forms.submitRequest", {
                              ka: "მოთხოვნის გაგზავნა",
                              en: "Send request",
                              ru: "Отправить запрос",
                          })}
                </button>
            </div>
        </form>
    );
}
