"use client";

import { CONSULTATION_POPOVER_ID } from "@/components/consultation/constants";
import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import { useLeadForm } from "@/hooks/useLeadForm";

export default function ConsultationForm() {
    const { status, message, submit } = useLeadForm("consultation-popup");
    const { t } = useLocalization();
    const firstNameLabel = t("forms.firstName", null);
    const lastNameLabel = t("forms.lastName", null);
    const phoneLabel = t("forms.phone", null);
    const emailLabel = t("forms.email", null);
    const detailsLabel = t("forms.details", null);
    const cancelLabel = t("common.cancel", null);
    const submittingLabel = t("forms.submitting", null);
    const submitLabel = t("forms.submitRequest", null);
    const privacyLabel = t("forms.privacy", null);

    if (
        !firstNameLabel ||
        !lastNameLabel ||
        !phoneLabel ||
        !cancelLabel ||
        !submitLabel ||
        !privacyLabel
    ) {
        return null;
    }

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
                    <span>{firstNameLabel}</span>
                    <input
                        autoComplete="given-name"
                        autoFocus
                        className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        name="firstName"
                        required
                        type="text"
                    />
                </label>
                <label className="space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>{lastNameLabel}</span>
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
                <span>{phoneLabel}</span>
                <input
                    autoComplete="tel"
                    className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                    inputMode="tel"
                    name="phone"
                    required
                    type="tel"
                />
            </label>
            {emailLabel ? (
                <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>{emailLabel}</span>
                    <input
                        autoComplete="email"
                        className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        name="email"
                        type="email"
                    />
                </label>
            ) : null}
            {detailsLabel ? (
                <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>{detailsLabel}</span>
                    <textarea
                        className="min-h-32 w-full resize-y rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        name="details"
                        rows={5}
                    />
                </label>
            ) : null}
            <PrivacyConsent label={privacyLabel} />
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
                    {cancelLabel}
                </button>
                <button
                    className="rounded-xl bg-primary-container px-6 py-3 font-medium text-on-primary-container shadow-lg shadow-blue-500/20 transition hover:brightness-110 disabled:cursor-wait disabled:opacity-60"
                    disabled={status === "submitting"}
                    type="submit"
                >
                    {status === "submitting"
                        ? submittingLabel || submitLabel
                        : submitLabel}
                </button>
            </div>
        </form>
    );
}
