"use client";

import { CONSULTATION_CLOSE_EVENT } from "@/components/consultation/constants";
import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import { useLeadForm } from "@/hooks/useLeadForm";

type ServiceOption = {
    slug: string;
    label: string;
};

export default function ConsultationForm({
    serviceOptions,
}: {
    serviceOptions: ServiceOption[];
}) {
    const { status, message, submit } = useLeadForm("consultation-popup");
    const { t } = useLocalization();
    const firstNameLabel = t("forms.firstName", {
        ka: "სახელი",
        en: "First name",
        ru: "Имя",
    });
    const lastNameLabel = t("forms.lastName", {
        ka: "გვარი",
        en: "Last name",
        ru: "Фамилия",
    });
    const phoneLabel = t("forms.phone", {
        ka: "ტელეფონის ნომერი",
        en: "Phone number",
        ru: "Номер телефона",
    });
    const emailLabel = t("forms.email", {
        ka: "ელფოსტა",
        en: "Email",
        ru: "Электронная почта",
    });
    const addressLabel = t("forms.address", {
        ka: "ქალაქი / მომსახურების მისამართი",
        en: "City / service address",
        ru: "Город / адрес оказания услуги",
    });
    const serviceLabel = t("forms.service", {
        ka: "სერვისი",
        en: "Service",
        ru: "Услуга",
    });
    const chooseServiceLabel = t("forms.chooseService", {
        ka: "აირჩიეთ სერვისი",
        en: "Select a service",
        ru: "Выберите услугу",
    });
    const servicesUnavailableLabel = t("forms.servicesUnavailable", {
        ka: "სერვისების სია დროებით მიუწვდომელია",
        en: "The service list is temporarily unavailable",
        ru: "Список услуг временно недоступен",
    });
    const detailsLabel = t("forms.details", {
        ka: "ამოცანის დეტალები",
        en: "Project details",
        ru: "Детали задачи",
    });
    const requiredHint = t("forms.requiredHint", {
        ka: "* ყველა ველი სავალდებულოა.",
        en: "* All fields are required.",
        ru: "* Все поля обязательны.",
    });
    const cancelLabel = t("common.cancel", {
        ka: "გაუქმება",
        en: "Cancel",
        ru: "Отмена",
    });
    const submittingLabel = t("forms.submitting", {
        ka: "იგზავნება…",
        en: "Sending…",
        ru: "Отправка…",
    });
    const submitLabel = t("forms.submitRequest", {
        ka: "კონსულტაციის მოთხოვნა",
        en: "Request consultation",
        ru: "Запросить консультацию",
    });
    const privacyLabel = t("forms.privacy", {
        ka: "ვეთანხმები ჩემი საკონტაქტო მონაცემების გამოყენებას კონსულტაციასთან დაკავშირებით დასაკავშირებლად.",
        en: "I agree to the use of my contact details so SafeTech can contact me about this consultation.",
        ru: "Я согласен на использование контактных данных, чтобы SafeTech мог связаться со мной по поводу консультации.",
    });
    const inputClassName =
        "w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20";
    const hasServices = serviceOptions.length > 0;

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
            <p className="text-sm text-on-surface-variant">{requiredHint}</p>
            <div className="grid gap-unit-md sm:grid-cols-2">
                <label className="space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>{firstNameLabel} *</span>
                    <input
                        autoComplete="given-name"
                        autoFocus
                        className={inputClassName}
                        maxLength={60}
                        minLength={2}
                        name="firstName"
                        required
                        type="text"
                    />
                </label>
                <label className="space-y-2 font-label-md text-label-md text-on-surface-variant">
                    <span>{lastNameLabel} *</span>
                    <input
                        autoComplete="family-name"
                        className={inputClassName}
                        maxLength={60}
                        minLength={2}
                        name="lastName"
                        required
                        type="text"
                    />
                </label>
            </div>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>{phoneLabel} *</span>
                <input
                    autoComplete="tel"
                    className={inputClassName}
                    inputMode="tel"
                    maxLength={24}
                    minLength={7}
                    name="phone"
                    required
                    type="tel"
                />
            </label>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>{emailLabel} *</span>
                <input
                    autoComplete="email"
                    className={inputClassName}
                    maxLength={160}
                    name="email"
                    required
                    type="email"
                />
            </label>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>{addressLabel} *</span>
                <input
                    autoComplete="street-address"
                    className={inputClassName}
                    maxLength={255}
                    minLength={2}
                    name="address"
                    required
                    type="text"
                />
            </label>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>{serviceLabel} *</span>
                <select
                    className={`${inputClassName} cursor-pointer disabled:cursor-not-allowed disabled:opacity-60`}
                    defaultValue=""
                    disabled={!hasServices}
                    name="serviceSlug"
                    required
                >
                    <option disabled value="">
                        {hasServices ? chooseServiceLabel : servicesUnavailableLabel}
                    </option>
                    {serviceOptions.map((service) => (
                        <option key={service.slug} value={service.slug}>
                            {service.label}
                        </option>
                    ))}
                </select>
            </label>
            <label className="block space-y-2 font-label-md text-label-md text-on-surface-variant">
                <span>{detailsLabel} *</span>
                <textarea
                    className={`${inputClassName} min-h-32 resize-y`}
                    maxLength={3000}
                    minLength={10}
                    name="details"
                    required
                    rows={5}
                />
            </label>
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
                    onClick={() => window.dispatchEvent(new Event(CONSULTATION_CLOSE_EVENT))}
                    type="button"
                >
                    {cancelLabel}
                </button>
                <button
                    className="rounded-xl bg-primary-container px-6 py-3 font-medium text-on-primary-container shadow-lg shadow-blue-500/20 transition hover:brightness-110 disabled:cursor-wait disabled:opacity-60"
                    disabled={status === "submitting" || !hasServices}
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
