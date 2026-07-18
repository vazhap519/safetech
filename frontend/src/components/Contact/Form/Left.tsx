"use client";

import { useState } from "react";

import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Button from "@/components/ui/Button";
import { Input, Select, Textarea } from "@/components/ui/Input";
import Typography from "@/components/ui/Typography";
import { useLeadForm } from "@/hooks/useLeadForm";
import type { ContactServiceOption, LeadFormField } from "@/lib/lead-form";

type LeftProps = {
    services: ContactServiceOption[];
};

type DynamicFieldProps = {
    field: LeadFormField;
};

function DynamicField({ field }: DynamicFieldProps) {
    const inputName = `details__${field.key}`;
    const inputId = `details-${field.key}`;

    return (
        <div className={field.type === "textarea" ? "md:col-span-2" : undefined}>
            <input
                name={`details_label__${field.key}`}
                readOnly
                type="hidden"
                value={field.label}
            />
            <input
                name={`details_type__${field.key}`}
                readOnly
                type="hidden"
                value={field.type}
            />

            {field.type === "select" ? (
                <Select
                    id={inputId}
                    label={field.label}
                    name={inputName}
                    options={field.options}
                    placeholder={field.placeholder}
                    required={field.required}
                />
            ) : field.type === "textarea" ? (
                <Textarea
                    id={inputId}
                    label={field.label}
                    name={inputName}
                    required={field.required}
                    rows={4}
                />
            ) : (
                <Input
                    id={inputId}
                    label={field.label}
                    name={inputName}
                    required={field.required}
                    type={field.type === "number" ? "number" : "text"}
                />
            )}
        </div>
    );
}

export default function Left({ services }: LeftProps) {
    const { status, message, submit } = useLeadForm("contact-page");
    const { t } = useLocalization();
    const [selectedServiceSlug, setSelectedServiceSlug] = useState(
        services[0]?.slug ?? "",
    );
    const selectedService =
        services.find((service) => service.slug === selectedServiceSlug) ??
        services[0] ??
        null;

    const title = t("contact.form.title", null);
    const chooseText = t("forms.choose", null);
    const contactHint = t("forms.contactHint", null);
    const fullNameLabel = t("forms.fullName", null);
    const companyLabel = t("forms.company", null);
    const phoneLabel = t("forms.phoneNumber", null);
    const emailLabel = t("forms.email", null);
    const serviceLabel = t("forms.service", null);
    const messageLabel = t("forms.message", null);
    const generalInquiry = t("forms.generalInquiry", null);
    const submittingLabel = t("forms.submitting", null);
    const sendLabel = t("forms.send", null);
    const privacyLabel = t("forms.privacy", null);

    if (
        !fullNameLabel ||
        !phoneLabel ||
        !emailLabel ||
        !messageLabel ||
        !sendLabel ||
        !privacyLabel ||
        (services.length > 0 && !serviceLabel)
    ) {
        return null;
    }

    return (
        <div className="glass-panel relative overflow-hidden rounded-2xl p-unit-md sm:p-unit-lg lg:col-span-7">
            <div
                aria-hidden="true"
                className="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-primary to-secondary opacity-50"
            />

            {title ? (
                <Typography as="h3" className="mb-unit-lg" variant="section-title">
                    {title}
                </Typography>
            ) : null}

            <form className="space-y-unit-lg" onSubmit={submit}>
                <input
                    aria-hidden="true"
                    autoComplete="off"
                    className="sr-only"
                    name="website"
                    tabIndex={-1}
                    type="text"
                />

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        autoComplete="name"
                        id="name"
                        label={fullNameLabel}
                        name="name"
                        required
                        type="text"
                    />

                    <Input
                        id="company"
                        label={companyLabel}
                        name="company"
                        type="text"
                    />
                </div>

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        autoComplete="tel"
                        id="phone"
                        label={phoneLabel}
                        name="phone"
                        type="tel"
                    />

                    <Input
                        autoComplete="email"
                        id="email"
                        label={emailLabel}
                        name="email"
                        type="email"
                    />
                </div>

                {contactHint ? (
                    <p className="text-sm text-on-surface-variant">
                        {contactHint}
                    </p>
                ) : null}

                <div className="space-y-unit-md">
                    {services.length ? (
                        <>
                            <Select
                                id="service"
                                label={serviceLabel}
                                name="service_slug"
                                onChange={(event) =>
                                    setSelectedServiceSlug(event.target.value)
                                }
                                options={services.map((service) => ({
                                    value: service.slug,
                                    label: service.label,
                                }))}
                                required
                                value={selectedService?.slug ?? ""}
                            />
                            {selectedService ? (
                                <input
                                    name="service"
                                    readOnly
                                    type="hidden"
                                    value={selectedService.label}
                                />
                            ) : null}
                        </>
                    ) : generalInquiry ? (
                        <p className="rounded-2xl border border-dashed border-outline-variant/30 bg-surface-container-low/30 px-4 py-3 text-sm text-on-surface-variant">
                            {generalInquiry}
                        </p>
                    ) : null}
                </div>

                {selectedService ? (
                    <div
                        className="grid gap-unit-md md:grid-cols-2"
                        key={selectedService.slug}
                    >
                        {selectedService.leadForm.projectSizeOptions.length ? (
                            <Select
                                id="project-size"
                                label={selectedService.leadForm.projectSizeLabel}
                                name="project-size"
                                options={selectedService.leadForm.projectSizeOptions.map(
                                    (option) => ({
                                        value: option.label,
                                        label: option.label,
                                    }),
                                )}
                                placeholder={chooseText}
                            />
                        ) : null}

                        {selectedService.leadForm.propertyTypeOptions.length ? (
                            <Select
                                id="property-type"
                                label={selectedService.leadForm.propertyTypeLabel}
                                name="property-type"
                                options={selectedService.leadForm.propertyTypeOptions.map(
                                    (option) => ({
                                        value: option.label,
                                        label: option.label,
                                    }),
                                )}
                                placeholder={chooseText}
                            />
                        ) : null}

                        {selectedService.leadForm.extraFields.map((field) => (
                            <DynamicField field={field} key={field.key} />
                        ))}
                    </div>
                ) : null}

                <Textarea
                    id="message"
                    label={messageLabel}
                    name="message"
                    rows={5}
                />

                <PrivacyConsent label={privacyLabel} />

                <Button
                    disabled={status === "submitting"}
                    fullWidth
                    size="lg"
                    type="submit"
                    variant="primary"
                >
                    {status === "submitting" ? submittingLabel || sendLabel : sendLabel}
                </Button>
                <p
                    aria-live="polite"
                    className={status === "error" ? "text-error" : "text-success"}
                    role="status"
                >
                    {message}
                </p>
            </form>
        </div>
    );
}
