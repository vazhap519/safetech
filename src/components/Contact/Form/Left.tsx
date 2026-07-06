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
                    name={inputName}
                    label={field.label}
                    options={field.options}
                    placeholder={field.placeholder}
                    required={field.required}
                />
            ) : field.type === "textarea" ? (
                <Textarea
                    id={inputId}
                    name={inputName}
                    label={field.label}
                    required={field.required}
                    rows={4}
                />
            ) : (
                <Input
                    id={inputId}
                    name={inputName}
                    label={field.label}
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
        services[0];

    if (!selectedService) {
        return null;
    }

    const chooseText = t("forms.choose", {
        ka: "აირჩიეთ",
        en: "Choose",
        ru: "Выберите",
    });

    return (
        <div
            className="
                glass-panel
                relative
                overflow-hidden
                rounded-2xl
                p-unit-md
                sm:p-unit-lg
                lg:col-span-7
            "
        >
            <div
                aria-hidden="true"
                className="
                    absolute
                    left-0
                    top-0
                    h-1
                    w-full
                    bg-gradient-to-r
                    from-primary
                    to-secondary
                    opacity-50
                "
            />

            <Typography
                as="h3"
                variant="section-title"
                className="mb-unit-lg"
            >
                {t("contact.form.title", {
                    ka: "საკონტაქტო ფორმა",
                    en: "Contact form",
                    ru: "Контактная форма",
                })}
            </Typography>

            <form className="space-y-unit-lg" onSubmit={submit}>
                <div
                    aria-hidden="true"
                    className="absolute -left-[9999px]"
                    tabIndex={-1}
                >
                    <label htmlFor="contact-website">Website</label>
                    <input
                        autoComplete="off"
                        id="contact-website"
                        name="website"
                        tabIndex={-1}
                        type="text"
                    />
                </div>

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        id="name"
                        name="name"
                        label={t("forms.fullName", {
                            ka: "სახელი და გვარი",
                            en: "Full name",
                            ru: "Имя и фамилия",
                        })}
                        type="text"
                        autoComplete="name"
                        required
                    />

                    <Input
                        id="company"
                        name="company"
                        label={t("forms.company", {
                            ka: "კომპანია",
                            en: "Company",
                            ru: "Компания",
                        })}
                        type="text"
                    />
                </div>

                <div className="grid gap-unit-md md:grid-cols-2">
                    <Input
                        id="phone"
                        name="phone"
                        label={t("forms.phoneNumber", {
                            ka: "ტელეფონის ნომერი",
                            en: "Phone number",
                            ru: "Номер телефона",
                        })}
                        type="tel"
                        autoComplete="tel"
                    />

                    <Input
                        id="email"
                        name="email"
                        label={t("forms.email", {
                            ka: "ელფოსტა",
                            en: "Email",
                            ru: "Email",
                        })}
                        type="email"
                        autoComplete="email"
                    />
                </div>

                <p className="text-sm text-on-surface-variant">
                    {t("forms.contactHint", {
                        ka: "მიუთითეთ ტელეფონის ნომერი ან ელფოსტა, რომ დაგიკავშირდეთ.",
                        en: "Provide a phone number or email address so we can contact you.",
                        ru: "Укажите номер телефона или email, чтобы мы могли с вами связаться.",
                    })}
                </p>

                <div className="space-y-unit-md">
                    <Select
                        id="service"
                        name="service_slug"
                        label={t("forms.service", {
                            ka: "სერვისი",
                            en: "Service",
                            ru: "Услуга",
                        })}
                        onChange={(event) =>
                            setSelectedServiceSlug(event.target.value)
                        }
                        options={services.map((service) => ({
                            value: service.slug,
                            label: service.label,
                        }))}
                        required
                        value={selectedService.slug}
                    />
                    <input
                        name="service"
                        readOnly
                        type="hidden"
                        value={selectedService.label}
                    />
                </div>

                <div
                    className="grid gap-unit-md md:grid-cols-2"
                    key={selectedService.slug}
                >
                    <Select
                        id="project-size"
                        name="project-size"
                        label={selectedService.leadForm.projectSizeLabel}
                        options={selectedService.leadForm.projectSizeOptions.map(
                            (option) => ({
                                value: option.label,
                                label: option.label,
                            }),
                        )}
                        placeholder={chooseText}
                    />

                    <Select
                        id="property-type"
                        name="property-type"
                        label={selectedService.leadForm.propertyTypeLabel}
                        options={selectedService.leadForm.propertyTypeOptions.map(
                            (option) => ({
                                value: option.label,
                                label: option.label,
                            }),
                        )}
                        placeholder={chooseText}
                    />

                    {selectedService.leadForm.extraFields.map((field) => (
                        <DynamicField field={field} key={field.key} />
                    ))}
                </div>

                <Textarea
                    id="message"
                    name="message"
                    label={t("forms.message", {
                        ka: "შეტყობინება",
                        en: "Message",
                        ru: "Сообщение",
                    })}
                    rows={5}
                />

                <PrivacyConsent />

                <Button
                    disabled={status === "submitting"}
                    type="submit"
                    variant="primary"
                    size="lg"
                    fullWidth
                >
                    {status === "submitting"
                        ? t("forms.submitting", {
                              ka: "იგზავნება...",
                              en: "Sending...",
                              ru: "Отправка...",
                          })
                        : t("forms.send", {
                              ka: "გაგზავნა",
                              en: "Send",
                              ru: "Отправить",
                          })}
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
