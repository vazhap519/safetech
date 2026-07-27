"use client";

import { useMemo, useState } from "react";

import PrivacyConsent from "@/components/forms/PrivacyConsent";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import LocalizedLink from "@/components/ui/LocalizedLink";
import Icon from "@/components/ui/Icon";
import { useLeadForm } from "@/hooks/useLeadForm";
import {
    calculateEstimateBreakdown,
    initialCalculatorValues,
    type CalculatorEstimateBreakdown,
    type CalculatorField,
    type CalculatorOption,
    type CalculatorProfile,
    type CalculatorValues,
} from "@/lib/service-calculator";

type ServiceCalculatorProps = {
    profiles: CalculatorProfile[];
    initialService?: string;
    variant?: "embedded" | "page";
};

const EMPTY_VALUES: CalculatorValues = {};
const EMPTY_BREAKDOWN: CalculatorEstimateBreakdown = {
    oneTime: 0,
    monthly: 0,
    lines: [],
};

function money(value: number, currency: string, locale: string) {
    const normalized = Number.isFinite(value) ? Math.max(0, value) : 0;
    const [whole, decimals] = normalized.toFixed(2).split(".");
    const groupSeparator = locale === "en" ? "," : " ";
    const decimalSeparator = locale === "en" ? "." : ",";
    const grouped = whole.replace(/\B(?=(\d{3})+(?!\d))/g, groupSeparator);
    const amount = decimals === "00"
        ? grouped
        : `${grouped}${decimalSeparator}${decimals}`;
    const symbols: Record<string, string> = {
        EUR: "EUR",
        GEL: "GEL",
        USD: "USD",
    };
    const symbol = symbols[currency] ?? currency;

    return `${amount} ${symbol}`.trim();
}

function FieldControl({
    field,
    value,
    onChange,
}: {
    field: CalculatorField;
    value: string | number | boolean;
    onChange: (value: string | number | boolean) => void;
}) {
    const baseClass =
        "min-h-12 w-full rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition-colors focus:border-primary";

    if (field.type === "checkbox") {
        return (
            <label className="flex min-h-12 cursor-pointer items-center gap-3 rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 py-3">
                <input
                    checked={Boolean(value)}
                    className="size-5 accent-primary"
                    onChange={(event) => onChange(event.target.checked)}
                    type="checkbox"
                />
                <span className="text-sm text-on-surface">{field.label}</span>
            </label>
        );
    }

    return (
        <label className="grid gap-2 text-sm text-on-surface-variant">
            <span>
                {field.label}
                {field.required ? " *" : ""}
            </span>
            {field.type === "select" ? (
                <select
                    className={baseClass}
                    onChange={(event) => onChange(event.target.value)}
                    required={field.required}
                    value={String(value ?? "")}
                >
                    <option value="">-</option>
                    {field.options.map((option) => (
                        <option key={option.value} value={option.value}>
                            {option.label}
                        </option>
                    ))}
                </select>
            ) : field.type === "textarea" ? (
                <textarea
                    className={`${baseClass} min-h-28 resize-y`}
                    onChange={(event) => onChange(event.target.value)}
                    placeholder={field.placeholder}
                    required={field.required}
                    value={String(value ?? "")}
                />
            ) : (
                <div className="relative">
                    <input
                        className={`${baseClass} ${field.unit ? "pr-16" : ""}`}
                        max={field.max ?? undefined}
                        min={field.min ?? undefined}
                        onChange={(event) => onChange(event.target.value)}
                        placeholder={field.placeholder}
                        required={field.required}
                        step={field.step ?? undefined}
                        type={field.type === "number" ? "number" : "text"}
                        value={String(value ?? "")}
                    />
                    {field.unit ? (
                        <span className="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-xs text-on-surface-variant">
                            {field.unit}
                        </span>
                    ) : null}
                </div>
            )}
            {field.help ? <span className="text-xs">{field.help}</span> : null}
        </label>
    );
}

function CalculatorSelect({
    label,
    onChange,
    options,
    value,
}: {
    label: string;
    onChange: (value: string) => void;
    options: CalculatorOption[];
    value: string;
}) {
    if (!options.length) return null;

    return (
        <label className="grid gap-2 text-sm text-on-surface-variant">
            <span>{label}</span>
            <select
                className="min-h-12 rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 text-on-surface outline-none focus:border-primary"
                onChange={(event) => onChange(event.target.value)}
                value={value}
            >
                <option value="">-</option>
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </label>
    );
}

export default function ServiceCalculator({
    profiles,
    initialService,
    variant = "page",
}: ServiceCalculatorProps) {
    const { locale, t } = useLocalization();
    const isEmbedded = variant === "embedded";
    const initialProfile =
        profiles.find((profile) => profile.slug === initialService) ?? profiles[0];
    const [serviceSlug, setServiceSlug] = useState(initialProfile?.slug ?? "");
    const profile =
        profiles.find((item) => item.slug === serviceSlug) ?? profiles[0];
    const [valuesByService, setValuesByService] = useState<
        Record<string, CalculatorValues>
    >(() =>
        Object.fromEntries(
            profiles.map((item) => [item.slug, initialCalculatorValues(item)]),
        ),
    );
    const [projectSizes, setProjectSizes] = useState<Record<string, string>>(() =>
        Object.fromEntries(
            profiles.map((item) => [
                item.slug,
                item.projectSize.options[0]?.value ?? "",
            ]),
        ),
    );
    const [propertyTypes, setPropertyTypes] = useState<Record<string, string>>(
        () =>
            Object.fromEntries(
                profiles.map((item) => [
                    item.slug,
                    item.propertyType.options[0]?.value ?? "",
                ]),
            ),
    );
    const [packages, setPackages] = useState<Record<string, string>>(() =>
        Object.fromEntries(
            profiles.map((item) => [
                item.slug,
                item.packages.find((entry) => entry.recommended)?.key ??
                    item.packages[0]?.key ??
                    "",
            ]),
        ),
    );
    const { status, message, submit } = useLeadForm("service-calculator");

    const values = useMemo(
        () => (profile ? valuesByService[profile.slug] ?? EMPTY_VALUES : EMPTY_VALUES),
        [profile, valuesByService],
    );
    const projectSize = profile ? projectSizes[profile.slug] ?? "" : "";
    const propertyType = profile ? propertyTypes[profile.slug] ?? "" : "";
    const packageKey = profile ? packages[profile.slug] ?? "" : "";
    const estimate = useMemo(
        () =>
            profile
                ? calculateEstimateBreakdown(
                      profile,
                      values,
                      projectSize,
                      propertyType,
                      packageKey,
                  )
                : EMPTY_BREAKDOWN,
        [profile, values, projectSize, propertyType, packageKey],
    );

    const copy = {
        eyebrow: t("calculator.hero.eyebrow", null),
        title: t("calculator.hero.title", null),
        description: t("calculator.hero.description", null),
        embeddedTitle: t("calculator.embed.title", {
            ka: "Advanced service calculator",
            en: "Advanced service calculator",
            ru: "Расширенный калькулятор услуг",
        }),
        embeddedDescription: t("calculator.embed.description", {
            ka: "აირჩიეთ სერვისი, დააზუსტეთ პარამეტრები და მიიღეთ დეტალური ფასის სტრუქტურა.",
            en: "Choose a service, adjust the scope, and get a detailed estimate breakdown.",
            ru: "Выберите услугу, уточните параметры и получите детальную структуру стоимости.",
        }),
        service: t("calculator.form.service", {
            ka: "სერვისი",
            en: "Service",
            ru: "Услуга",
        }),
        package: t("calculator.form.package", {
            ka: "მომსახურების პაკეტი",
            en: "Service package",
            ru: "Пакет услуг",
        }),
        recommended: t("calculator.form.recommended", {
            ka: "რეკომენდებული",
            en: "Recommended",
            ru: "Рекомендуем",
        }),
        oneTime: t("calculator.summary.oneTime", {
            ka: "ერთჯერადი ბიუჯეტი",
            en: "One-time estimate",
            ru: "Разовый бюджет",
        }),
        monthly: t("calculator.summary.monthly", {
            ka: "ყოველთვიური მომსახურება",
            en: "Monthly service",
            ru: "Ежемесячное обслуживание",
        }),
        breakdown: t("calculator.summary.breakdown", {
            ka: "დეტალური breakdown",
            en: "Detailed breakdown",
            ru: "Детальная разбивка",
        }),
        baseLine: t("calculator.summary.base", {
            ka: "საბაზო ღირებულება",
            en: "Base service cost",
            ru: "Базовая стоимость",
        }),
        minimumBudget: t("calculator.summary.minimum", {
            ka: "მინიმალური ბიუჯეტი",
            en: "Minimum project budget",
            ru: "Минимальный бюджет проекта",
        }),
        included: t("calculator.summary.included", {
            ka: "ჩართულია",
            en: "Included",
            ru: "Включено",
        }),
        perMonth: t("calculator.summary.perMonth", {
            ka: "თვეში",
            en: "per month",
            ru: "в месяц",
        }),
        contactTitle: t("calculator.contact.title", {
            ka: "მიიღეთ ზუსტი შეთავაზება",
            en: "Get an exact proposal",
            ru: "Получите точное предложение",
        }),
        name: t("forms.fullName", {
            ka: "სახელი და გვარი",
            en: "Full name",
            ru: "Имя и фамилия",
        }),
        phone: t("forms.phoneNumber", {
            ka: "ტელეფონი",
            en: "Phone",
            ru: "Телефон",
        }),
        email: t("forms.email", {
            ka: "ელფოსტა",
            en: "Email",
            ru: "Email",
        }),
        privacy: t("forms.privacy", {
            ka: "ვეთანხმები პერსონალური მონაცემების დამუშავებას",
            en: "I agree to the processing of personal data",
            ru: "Я согласен на обработку персональных данных",
        }),
        send: t("calculator.contact.send", {
            ka: "შეთავაზების მოთხოვნა",
            en: "Request proposal",
            ru: "Запросить предложение",
        }),
        sending: t("forms.submitting", {
            ka: "იგზავნება...",
            en: "Sending...",
            ru: "Отправка...",
        }),
        print: t("calculator.summary.print", {
            ka: "ბეჭდვა",
            en: "Print",
            ru: "Печать",
        }),
        openFull: t("calculator.actions.full", {
            ka: "სრული კალკულატორი",
            en: "Full calculator",
            ru: "Полный калькулятор",
        }),
        contactPage: t("calculator.actions.contact", {
            ka: "კონტაქტი",
            en: "Contact",
            ru: "Контакты",
        }),
        empty: t("calculator.empty", {
            ka: "კალკულატორის პროფილები ჯერ არ არის დამატებული. ჩართეთ სასურველი სერვისი ადმინის პანელიდან.",
            en: "No calculator profiles are available yet. Enable a service from the admin panel.",
            ru: "Профили калькулятора пока не добавлены. Включите услугу в панели администратора.",
        }),
    };

    if (!profile) {
        return isEmbedded ? (
            <div className="glass-card rounded-3xl border border-outline-variant/20 p-6">
                <p className="text-sm text-on-surface-variant">{copy.empty}</p>
            </div>
        ) : (
            <section className="mx-auto min-h-[64svh] max-w-4xl px-5 pb-16 pt-32 text-center md:pt-36">
                {copy.title ? (
                    <h1 className="text-[30px] font-semibold leading-[1.2] text-on-surface sm:text-[38px] md:text-[44px]">
                        {copy.title}
                    </h1>
                ) : null}
                <p className="mx-auto mt-6 max-w-2xl text-on-surface-variant">
                    {copy.empty}
                </p>
            </section>
        );
    }

    const updateField = (fieldKey: string, nextValue: string | number | boolean) => {
        setValuesByService((current) => ({
            ...current,
            [profile.slug]: {
                ...(current[profile.slug] ?? {}),
                [fieldKey]: nextValue,
            },
        }));
    };

    const selectService = (nextSlug: string) => {
        setServiceSlug(nextSlug);

        if (isEmbedded) {
            return;
        }

        const url = new URL(window.location.href);
        url.searchParams.set("service", nextSlug);
        window.history.replaceState({}, "", url);
    };

    const selectedProjectLabel = profile.projectSize.options.find(
        (option) => option.value === projectSize,
    )?.label;
    const selectedPropertyLabel = profile.propertyType.options.find(
        (option) => option.value === propertyType,
    )?.label;
    const selectedPackage = profile.packages.find(
        (item) => item.key === packageKey,
    );
    const fullCalculatorHref = `/service-calculator?service=${encodeURIComponent(profile.slug)}`;

    const controls = (
        <>
            <label className="grid gap-2 text-sm text-on-surface-variant">
                <span>{copy.service}</span>
                <select
                    className="min-h-14 rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 text-base font-medium text-on-surface outline-none focus:border-primary"
                    onChange={(event) => selectService(event.target.value)}
                    value={profile.slug}
                >
                    {profiles.map((item) => (
                        <option key={item.slug} value={item.slug}>
                            {item.name}
                        </option>
                    ))}
                </select>
            </label>

            <div className={`grid gap-5 ${isEmbedded ? "" : "sm:grid-cols-2"}`}>
                <CalculatorSelect
                    label={profile.projectSize.label}
                    onChange={(value) =>
                        setProjectSizes((current) => ({
                            ...current,
                            [profile.slug]: value,
                        }))
                    }
                    options={profile.projectSize.options}
                    value={projectSize}
                />
                <CalculatorSelect
                    label={profile.propertyType.label}
                    onChange={(value) =>
                        setPropertyTypes((current) => ({
                            ...current,
                            [profile.slug]: value,
                        }))
                    }
                    options={profile.propertyType.options}
                    value={propertyType}
                />
            </div>

            {profile.fields.length ? (
                <div className={`grid gap-5 ${isEmbedded ? "" : "sm:grid-cols-2"}`}>
                    {profile.fields.map((field) => (
                        <div
                            className={
                                !isEmbedded && field.type === "textarea"
                                    ? "sm:col-span-2"
                                    : ""
                            }
                            key={field.key}
                        >
                            <FieldControl
                                field={field}
                                onChange={(nextValue) =>
                                    updateField(field.key, nextValue)
                                }
                                value={values[field.key] ?? ""}
                            />
                        </div>
                    ))}
                </div>
            ) : null}

            {profile.packages.length ? (
                <fieldset className="space-y-3">
                    <legend className="mb-3 text-lg font-semibold text-on-surface">
                        {copy.package}
                    </legend>
                    {profile.packages.map((item) => (
                        <label
                            className="flex cursor-pointer items-start gap-4 rounded-lg border border-outline-variant/30 bg-surface-container-low/60 p-4 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/10"
                            key={item.key}
                        >
                            <input
                                checked={packageKey === item.key}
                                className="mt-1 size-5 accent-primary"
                                name={`calculator-package-${variant}`}
                                onChange={() =>
                                    setPackages((current) => ({
                                        ...current,
                                        [profile.slug]: item.key,
                                    }))
                                }
                                type="radio"
                            />
                            <span className="min-w-0 flex-1">
                                <span className="flex flex-wrap items-center gap-2 font-medium text-on-surface">
                                    {item.title}
                                    {item.recommended ? (
                                        <span className="rounded bg-secondary/15 px-2 py-1 text-xs text-secondary">
                                            {copy.recommended}
                                        </span>
                                    ) : null}
                                </span>
                                {item.description ? (
                                    <span className="mt-1 block text-sm text-on-surface-variant">
                                        {item.description}
                                    </span>
                                ) : null}
                            </span>
                        </label>
                    ))}
                </fieldset>
            ) : null}
        </>
    );

    const summary = (
        <section
            className={
                isEmbedded
                    ? "rounded-2xl border border-outline-variant/20 bg-surface p-5"
                    : "h-fit border-t border-outline-variant/30 pt-6 lg:sticky lg:top-28 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
            }
        >
            <div className="flex items-center justify-between gap-4">
                <span className="text-sm text-on-surface-variant">
                    {copy.oneTime}
                </span>
                <Icon className="text-secondary" name="chart" />
            </div>
            <p className="mt-2 break-words text-4xl font-semibold text-on-surface">
                {money(estimate.oneTime, profile.currency, locale)}
            </p>
            {estimate.monthly > 0 ? (
                <div className="mt-6 border-t border-outline-variant/20 pt-5">
                    <p className="text-sm text-on-surface-variant">
                        {copy.monthly}
                    </p>
                    <p className="mt-1 text-2xl font-semibold text-secondary">
                        {money(estimate.monthly, profile.currency, locale)}
                    </p>
                </div>
            ) : null}

            {estimate.lines.length ? (
                <div className="mt-6 border-t border-outline-variant/20 pt-5">
                    <p className="text-sm font-medium text-on-surface">
                        {copy.breakdown}
                    </p>
                    <div className="mt-4 space-y-3">
                        {estimate.lines.map((line) => {
                            const lineLabel = line.kind === "base"
                                ? copy.baseLine
                                : line.kind === "minimum"
                                  ? copy.minimumBudget
                                  : line.kind === "package"
                                    ? copy.package
                                    : line.label;
                            const lineAmounts = [
                                line.oneTime > 0
                                    ? `+ ${money(line.oneTime, profile.currency, locale)}`
                                    : "",
                                line.monthly > 0
                                    ? `+ ${money(line.monthly, profile.currency, locale)} ${copy.perMonth}`
                                    : "",
                            ].filter(Boolean);

                            return (
                                <div
                                    className="rounded-xl border border-outline-variant/20 bg-surface-container-low p-3"
                                    key={line.key}
                                >
                                    <div className="flex items-start justify-between gap-3">
                                        <div className="min-w-0">
                                            <p className="text-sm font-medium text-on-surface">
                                                {lineLabel}
                                            </p>
                                            {line.detail ? (
                                                <p className="mt-1 text-xs text-on-surface-variant">
                                                    {line.detail}
                                                </p>
                                            ) : null}
                                        </div>
                                        <div className="max-w-[10rem] text-right text-xs text-on-surface-variant">
                                            {lineAmounts.length ? (
                                                lineAmounts.map((amount) => (
                                                    <p key={amount}>{amount}</p>
                                                ))
                                            ) : (
                                                <p>{copy.included}</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </div>
            ) : null}

            {profile.disclaimer ? (
                <p className="mt-6 text-sm leading-6 text-on-surface-variant">
                    {profile.disclaimer}
                </p>
            ) : null}

            {isEmbedded ? (
                <div className="mt-6 grid gap-3 sm:grid-cols-2">
                    <LocalizedLink
                        className="inline-flex min-h-11 items-center justify-center rounded-lg bg-primary px-4 text-sm font-semibold text-on-primary transition-colors hover:bg-primary/90"
                        href={fullCalculatorHref}
                    >
                        {copy.openFull}
                    </LocalizedLink>
                    <LocalizedLink
                        className="inline-flex min-h-11 items-center justify-center rounded-lg border border-outline-variant/30 px-4 text-sm font-medium text-on-surface transition-colors hover:border-primary"
                        href="/contact"
                    >
                        {copy.contactPage}
                    </LocalizedLink>
                </div>
            ) : (
                <button
                    className="mt-6 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-lg border border-outline-variant/40 px-4 text-sm font-medium text-on-surface transition-colors hover:border-primary print:hidden"
                    onClick={() => window.print()}
                    type="button"
                >
                    <Icon name="draft" />
                    {copy.print}
                </button>
            )}
        </section>
    );

    if (isEmbedded) {
        return (
            <div className="glass-card rounded-3xl border border-outline-variant/20 bg-surface-container/70 p-6 shadow-[0_20px_60px_rgba(15,23,42,0.08)]">
                <div className="mb-6 space-y-3">
                    <div className="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-primary">
                        <Icon className="text-base" name="query_stats" />
                        {copy.embeddedTitle}
                    </div>
                    <p className="text-sm leading-6 text-on-surface-variant">
                        {copy.embeddedDescription}
                    </p>
                </div>
                <div className="space-y-6">
                    {controls}
                    {summary}
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-background pb-20 pt-[76px] print:bg-white print:pb-0 print:pt-0">
            <section className="border-b border-outline-variant/20 px-5 py-10 sm:py-14">
                <div className="mx-auto max-w-6xl">
                    {copy.eyebrow ? (
                        <p className="text-sm font-semibold uppercase text-secondary">
                            {copy.eyebrow}
                        </p>
                    ) : null}
                    {copy.title ? (
                        <h1 className="mt-3 max-w-4xl text-[32px] font-semibold leading-[1.2] text-on-surface sm:text-[40px] md:text-[48px]">
                            {copy.title}
                        </h1>
                    ) : null}
                    {copy.description ? (
                        <p className="mt-5 max-w-3xl text-base leading-7 text-on-surface-variant sm:text-lg">
                            {copy.description}
                        </p>
                    ) : null}
                </div>
            </section>

            <form
                className="mx-auto grid max-w-6xl gap-10 px-5 py-10 lg:grid-cols-[minmax(0,1fr)_22rem]"
                onSubmit={submit}
            >
                <div className="space-y-9">
                    {controls}

                    <section className="border-t border-outline-variant/20 pt-8 print:hidden">
                        <h2 className="text-2xl font-semibold text-on-surface">
                            {copy.contactTitle}
                        </h2>
                        <div className="mt-5 grid gap-4 sm:grid-cols-3">
                            <label className="grid gap-2 text-sm text-on-surface-variant">
                                <span>{copy.name}</span>
                                <input
                                    className="min-h-12 rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 text-on-surface outline-none focus:border-primary"
                                    name="name"
                                    required
                                />
                            </label>
                            <label className="grid gap-2 text-sm text-on-surface-variant">
                                <span>{copy.phone}</span>
                                <input
                                    autoComplete="tel"
                                    className="min-h-12 rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 text-on-surface outline-none focus:border-primary"
                                    name="phone"
                                    type="tel"
                                />
                            </label>
                            <label className="grid gap-2 text-sm text-on-surface-variant">
                                <span>{copy.email}</span>
                                <input
                                    autoComplete="email"
                                    className="min-h-12 rounded-lg border border-outline-variant/30 bg-surface-container-low px-4 text-on-surface outline-none focus:border-primary"
                                    name="email"
                                    type="email"
                                />
                            </label>
                        </div>

                        <input
                            name="service_slug"
                            readOnly
                            type="hidden"
                            value={profile.slug}
                        />
                        <input
                            name="service"
                            readOnly
                            type="hidden"
                            value={profile.name}
                        />
                        <input
                            name="project-size"
                            readOnly
                            type="hidden"
                            value={selectedProjectLabel ?? ""}
                        />
                        <input
                            name="property-type"
                            readOnly
                            type="hidden"
                            value={selectedPropertyLabel ?? ""}
                        />
                        <input
                            name="details__package"
                            readOnly
                            type="hidden"
                            value={selectedPackage?.title ?? ""}
                        />
                        <input
                            name="details_label__package"
                            readOnly
                            type="hidden"
                            value={copy.package}
                        />
                        <input
                            name="details_type__package"
                            readOnly
                            type="hidden"
                            value="select"
                        />
                        <input
                            name="details__one_time_estimate"
                            readOnly
                            type="hidden"
                            value={`${estimate.oneTime} ${profile.currency}`}
                        />
                        <input
                            name="details_label__one_time_estimate"
                            readOnly
                            type="hidden"
                            value={copy.oneTime}
                        />
                        <input
                            name="details_type__one_time_estimate"
                            readOnly
                            type="hidden"
                            value="money"
                        />
                        <input
                            name="details__monthly_estimate"
                            readOnly
                            type="hidden"
                            value={`${estimate.monthly} ${profile.currency}`}
                        />
                        <input
                            name="details_label__monthly_estimate"
                            readOnly
                            type="hidden"
                            value={copy.monthly}
                        />
                        <input
                            name="details_type__monthly_estimate"
                            readOnly
                            type="hidden"
                            value="money"
                        />
                        {profile.fields.map((field) => (
                            <span key={`lead-${field.key}`}>
                                <input
                                    name={`details__${field.key}`}
                                    readOnly
                                    type="hidden"
                                    value={String(values[field.key] ?? "")}
                                />
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
                            </span>
                        ))}

                        <div className="mt-5">
                            <PrivacyConsent label={copy.privacy} />
                        </div>
                        <button
                            className="mt-5 inline-flex min-h-12 items-center justify-center gap-2 rounded-lg bg-primary-container px-6 font-semibold text-on-primary-container transition-colors hover:bg-primary disabled:cursor-wait disabled:opacity-60"
                            disabled={status === "submitting"}
                            type="submit"
                        >
                            <Icon name="mail" />
                            {status === "submitting" ? copy.sending : copy.send}
                        </button>
                        <p
                            aria-live="polite"
                            className={`mt-3 text-sm ${status === "error" ? "text-error" : "text-success"}`}
                            role="status"
                        >
                            {message}
                        </p>
                    </section>
                </div>

                {summary}
            </form>
        </div>
    );
}
