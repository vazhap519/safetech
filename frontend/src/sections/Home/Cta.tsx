"use client";

import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Button from "@/components/ui/Button";
import Typography from "@/components/ui/Typography";
import { useLeadForm } from "@/hooks/useLeadForm";

export default function Cta() {
    const { status, message, submit } = useLeadForm("home-cta");
    const { t } = useLocalization();
    const eyebrow = t("home.cta.eyebrow", null);
    const title = t("home.cta.title", null);
    const description = t("home.cta.description", null);
    const emailLabel = t("home.cta.emailLabel", null);
    const emailPlaceholder = t("home.cta.emailPlaceholder", null);
    const submitLabel = t("home.cta.submit", null);
    const submittingLabel = t("forms.submitting", null);
    const privacyLabel = t("forms.privacy", null);
    const note = t("home.cta.note", null);
    const canRenderForm = Boolean(emailLabel && submitLabel && privacyLabel);

    if (
        !eyebrow &&
        !title &&
        !description &&
        !canRenderForm &&
        !note
    ) {
        return null;
    }

    return (
        <section className="relative mx-auto max-w-container-max overflow-hidden px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14">
            <div className="mesh-gradient relative z-10 overflow-hidden rounded-2xl border border-outline-variant/20 px-6 py-12 text-center sm:px-10 md:px-14 md:py-16">
                <div className="absolute inset-0 bg-primary/5 opacity-70 blur-[120px]" />

                <div className="relative z-20 mx-auto max-w-4xl space-y-6">
                    {eyebrow ? (
                        <span className="inline-block font-mono-sm text-mono-sm font-semibold uppercase text-primary">
                            {eyebrow}
                        </span>
                    ) : null}

                    {title ? (
                        <Typography as="h2" variant="cta-title">
                            {title}
                        </Typography>
                    ) : null}

                    {description ? (
                        <Typography
                            as="p"
                            className="mx-auto max-w-2xl"
                            variant="section-description"
                        >
                            {description}
                        </Typography>
                    ) : null}

                    {canRenderForm ? (
                        <form
                            className="mx-auto w-full max-w-2xl space-y-4 pt-2"
                            onSubmit={submit}
                        >
                            <input
                                aria-hidden="true"
                                autoComplete="off"
                                className="sr-only"
                                name="website"
                                tabIndex={-1}
                                type="text"
                            />

                            <div className="flex flex-col items-center justify-center gap-4 md:flex-row">
                                <label className="sr-only" htmlFor="home-cta-email">
                                    {emailLabel}
                                </label>
                                <input
                                    id="home-cta-email"
                                    autoComplete="email"
                                    className="w-full rounded-xl border border-outline-variant/30 bg-surface-container-highest/50 px-6 py-4 font-body-md text-on-surface outline-none backdrop-blur-xl transition-all duration-300 focus:border-transparent focus:ring-2 focus:ring-primary md:flex-1"
                                    name="email"
                                    placeholder={emailPlaceholder}
                                    required
                                    type="email"
                                />

                                <Button
                                    disabled={status === "submitting"}
                                    size="lg"
                                    type="submit"
                                    variant="cta"
                                >
                                    {status === "submitting"
                                        ? submittingLabel || submitLabel
                                        : submitLabel}
                                </Button>
                            </div>
                            <PrivacyConsent label={privacyLabel} />
                        </form>
                    ) : null}
                    <p
                        aria-live="polite"
                        className={status === "error" ? "text-error" : "text-success"}
                        role="status"
                    >
                        {message}
                    </p>

                    {note ? (
                        <p className="text-sm font-label-md leading-relaxed text-on-surface-variant/50 md:text-label-md">
                            {note}
                        </p>
                    ) : null}
                </div>
            </div>

        </section>
    );
}
