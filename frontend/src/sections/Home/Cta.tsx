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
    const nameLabel = t("forms.fullName", {
        ka: "სახელი და გვარი",
        en: "Full name",
        ru: "Имя и фамилия",
    });
    const phoneLabel = t("forms.phoneNumber", {
        ka: "ტელეფონის ნომერი",
        en: "Phone number",
        ru: "Номер телефона",
    });
    const emailLabel = t("home.cta.emailLabel", {
        ka: "ელფოსტა",
        en: "Email",
        ru: "Электронная почта",
    });
    const emailPlaceholder = t("home.cta.emailPlaceholder", {
        ka: "name@example.com",
        en: "name@example.com",
        ru: "name@example.com",
    });
    const addressLabel = t("forms.address", {
        ka: "ქალაქი / მომსახურების მისამართი",
        en: "City / service address",
        ru: "Город / адрес оказания услуги",
    });
    const serviceLabel = t("forms.service", {
        ka: "რომელი მომსახურება გჭირდებათ",
        en: "Service you need",
        ru: "Необходимая услуга",
    });
    const messageLabel = t("forms.message", {
        ka: "მოთხოვნის მოკლე აღწერა",
        en: "Request details",
        ru: "Описание запроса",
    });
    const submitLabel = t("home.cta.submit", {
        ka: "მოთხოვნის გაგზავნა",
        en: "Send request",
        ru: "Отправить запрос",
    });
    const submittingLabel = t("forms.submitting", null);
    const privacyLabel = t("forms.privacy", {
        ka: "ვეთანხმები ჩემი საკონტაქტო მონაცემების გამოყენებას მოთხოვნაზე პასუხისთვის.",
        en: "I agree to the use of my contact details to respond to this request.",
        ru: "Я согласен на использование контактных данных для ответа на запрос.",
    });
    const note = t("home.cta.note", null);
    const canRenderForm = Boolean(submitLabel && privacyLabel);
    const inputClassName =
        "w-full rounded-xl border border-outline-variant/30 bg-surface-container-highest/50 px-5 py-4 font-body-md text-on-surface outline-none backdrop-blur-xl transition-all duration-300 placeholder:text-on-surface-variant/60 focus:border-transparent focus:ring-2 focus:ring-primary";

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
                            className="mx-auto w-full max-w-3xl space-y-4 pt-2 text-left"
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

                            <div className="grid gap-4 md:grid-cols-2">
                                <label className="space-y-2 text-sm font-medium text-on-surface-variant">
                                    <span>{nameLabel} *</span>
                                    <input
                                        autoComplete="name"
                                        className={inputClassName}
                                        name="name"
                                        required
                                        type="text"
                                    />
                                </label>
                                <label className="space-y-2 text-sm font-medium text-on-surface-variant">
                                    <span>{phoneLabel} *</span>
                                    <input
                                        autoComplete="tel"
                                        className={inputClassName}
                                        inputMode="tel"
                                        name="phone"
                                        required
                                        type="tel"
                                    />
                                </label>
                                <label className="space-y-2 text-sm font-medium text-on-surface-variant">
                                    <span>{emailLabel} *</span>
                                    <input
                                        autoComplete="email"
                                        className={inputClassName}
                                        name="email"
                                        placeholder={emailPlaceholder}
                                        required
                                        type="email"
                                    />
                                </label>
                                <label className="space-y-2 text-sm font-medium text-on-surface-variant">
                                    <span>{addressLabel} *</span>
                                    <input
                                        autoComplete="street-address"
                                        className={inputClassName}
                                        name="address"
                                        required
                                        type="text"
                                    />
                                </label>
                            </div>

                            <label className="block space-y-2 text-sm font-medium text-on-surface-variant">
                                <span>{serviceLabel} *</span>
                                <input
                                    className={inputClassName}
                                    name="service"
                                    required
                                    type="text"
                                />
                            </label>

                            <label className="block space-y-2 text-sm font-medium text-on-surface-variant">
                                <span>{messageLabel} *</span>
                                <textarea
                                    className={`${inputClassName} min-h-32 resize-y`}
                                    name="message"
                                    required
                                    rows={4}
                                />
                            </label>

                            <PrivacyConsent label={privacyLabel} />

                            <div className="flex justify-center pt-2">
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
