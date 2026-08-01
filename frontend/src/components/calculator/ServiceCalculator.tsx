"use client";

import { useMemo, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
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

    return `${amount} ${currency}`.trim();
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
                className="min-h-12 rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 text-on-surface outline-none transition-colors focus:border-primary"
                onChange={(event) => onChange(event.target.value)}
                value={value}
            >
                {options.map((option) => (
                    <option key={option.value} value={option.value}>
                        {option.label}
                    </option>
                ))}
            </select>
        </label>
    );
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
        "min-h-12 w-full rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3 text-on-surface outline-none transition-colors focus:border-primary";

    if (field.type === "checkbox") {
        return (
            <label className="flex min-h-12 cursor-pointer items-center gap-3 rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 py-3">
                <input
                    checked={Boolean(value)}
                    className="size-5 accent-primary"
                    onChange={(event) => onChange(event.target.checked)}
                    type="checkbox"
                />
                <span className="text-sm font-medium text-on-surface">
                    {field.label}
                </span>
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

export default function ServiceCalculator({
    profiles,
    initialService,
}: ServiceCalculatorProps) {
    const { locale, t } = useLocalization();
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
        eyebrow: t("calculator.embed.eyebrow", {
            ka: "საორიენტაციო ღირებულება",
            en: "Indicative pricing",
            ru: "Ориентировочная стоимость",
        }),
        title: t("calculator.embed.title", {
            ka: "სერვისების ფასის კალკულატორი",
            en: "Service price calculator",
            ru: "Калькулятор стоимости услуг",
        }),
        description: t("calculator.embed.description", {
            ka: "აირჩიეთ სერვისი და მიუთითეთ პროექტის ძირითადი პარამეტრები. შედეგი ავტომატურად განახლდება.",
            en: "Choose a service and enter the main project parameters. The estimate updates automatically.",
            ru: "Выберите услугу и укажите основные параметры проекта. Расчет обновится автоматически.",
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
            ka: "საორიენტაციო ერთჯერადი ღირებულება",
            en: "Indicative one-time cost",
            ru: "Ориентировочная разовая стоимость",
        }),
        monthly: t("calculator.summary.monthly", {
            ka: "ყოველთვიური მომსახურება",
            en: "Monthly service",
            ru: "Ежемесячное обслуживание",
        }),
        breakdown: t("calculator.summary.breakdown", {
            ka: "ღირებულების შემადგენლობა",
            en: "Cost breakdown",
            ru: "Состав стоимости",
        }),
        baseLine: t("calculator.summary.base", {
            ka: "საბაზო მომსახურება",
            en: "Base service cost",
            ru: "Базовая стоимость",
        }),
        minimumBudget: t("calculator.summary.minimum", {
            ka: "მინიმალური ბიუჯეტი",
            en: "Minimum project budget",
            ru: "Минимальный бюджет",
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
        exactQuote: t("calculator.actions.contact", {
            ka: "ზუსტი შეთავაზების მოთხოვნა",
            en: "Request an exact quote",
            ru: "Запросить точное предложение",
        }),
    };

    if (!profile) return null;

    const updateField = (
        fieldKey: string,
        nextValue: string | number | boolean,
    ) => {
        setValuesByService((current) => ({
            ...current,
            [profile.slug]: {
                ...(current[profile.slug] ?? {}),
                [fieldKey]: nextValue,
            },
        }));
    };

    return (
        <section className="scroll-mt-28 pt-unit-2xl" id="service-calculator">
            <div className="overflow-hidden rounded-3xl border border-outline-variant/20 bg-surface-container/70 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                <div className="border-b border-outline-variant/20 bg-primary/5 px-5 py-7 sm:px-8 sm:py-9">
                    <div className="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-primary">
                        <Icon className="text-base" name="calculate" />
                        {copy.eyebrow}
                    </div>
                    <h2 className="mt-4 max-w-3xl text-2xl font-semibold leading-tight text-on-surface sm:text-3xl">
                        {copy.title}
                    </h2>
                    <p className="mt-3 max-w-3xl text-sm leading-6 text-on-surface-variant sm:text-base">
                        {copy.description}
                    </p>
                </div>

                <div className="grid gap-8 p-5 sm:p-8 lg:grid-cols-[minmax(0,1fr)_23rem] lg:items-start">
                    <div className="space-y-7">
                        <label className="grid gap-2 text-sm text-on-surface-variant">
                            <span>{copy.service}</span>
                            <select
                                className="min-h-14 rounded-xl border border-outline-variant/30 bg-surface-container-low px-4 text-base font-semibold text-on-surface outline-none transition-colors focus:border-primary"
                                onChange={(event) => setServiceSlug(event.target.value)}
                                value={profile.slug}
                            >
                                {profiles.map((item) => (
                                    <option key={item.slug} value={item.slug}>
                                        {item.name}
                                    </option>
                                ))}
                            </select>
                        </label>

                        {profile.description ? (
                            <p className="rounded-xl border border-outline-variant/20 bg-surface-container-low px-4 py-3 text-sm leading-6 text-on-surface-variant">
                                {profile.description}
                            </p>
                        ) : null}

                        <div className="grid gap-5 sm:grid-cols-2">
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
                            <div className="grid gap-5 sm:grid-cols-2">
                                {profile.fields.map((field) => (
                                    <div
                                        className={
                                            field.type === "textarea"
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
                                <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    {profile.packages.map((item) => (
                                        <label
                                            className="flex cursor-pointer items-start gap-3 rounded-xl border border-outline-variant/30 bg-surface-container-low/60 p-4 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/10"
                                            key={item.key}
                                        >
                                            <input
                                                checked={packageKey === item.key}
                                                className="mt-1 size-5 accent-primary"
                                                name="service-calculator-package"
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
                                                    <span className="mt-1 block text-sm leading-5 text-on-surface-variant">
                                                        {item.description}
                                                    </span>
                                                ) : null}
                                            </span>
                                        </label>
                                    ))}
                                </div>
                            </fieldset>
                        ) : null}
                    </div>

                    <aside className="rounded-2xl border border-outline-variant/20 bg-surface p-5 lg:sticky lg:top-28">
                        <div className="flex items-center justify-between gap-4">
                            <span className="text-sm text-on-surface-variant">
                                {copy.oneTime}
                            </span>
                            <Icon className="text-secondary" name="query_stats" />
                        </div>
                        <p className="mt-2 break-words text-4xl font-semibold text-on-surface">
                            {money(estimate.oneTime, profile.currency, locale)}
                        </p>

                        {estimate.monthly > 0 ? (
                            <div className="mt-5 border-t border-outline-variant/20 pt-5">
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
                                <p className="text-sm font-semibold text-on-surface">
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
                                        const amounts = [
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
                                                <p className="text-sm font-medium text-on-surface">
                                                    {lineLabel}
                                                </p>
                                                {line.detail ? (
                                                    <p className="mt-1 text-xs text-on-surface-variant">
                                                        {line.detail}
                                                    </p>
                                                ) : null}
                                                <div className="mt-2 text-xs font-medium text-secondary">
                                                    {amounts.length ? (
                                                        amounts.map((amount) => (
                                                            <p key={amount}>{amount}</p>
                                                        ))
                                                    ) : (
                                                        <p>{copy.included}</p>
                                                    )}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        ) : null}

                        {profile.disclaimer ? (
                            <p className="mt-5 text-xs leading-5 text-on-surface-variant">
                                {profile.disclaimer}
                            </p>
                        ) : null}

                        <LocalizedLink
                            className="mt-6 inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-xl bg-primary px-5 text-sm font-semibold text-on-primary transition-colors hover:bg-primary/90"
                            href="/contact"
                        >
                            <Icon name="mail" />
                            {copy.exactQuote}
                        </LocalizedLink>
                    </aside>
                </div>
            </div>
        </section>
    );
}
