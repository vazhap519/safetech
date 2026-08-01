"use client";

import { useMemo, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
import {
    calculateConfiguratorTotals,
    calculateEstimateBreakdown,
    getCompatibleComponents,
    initialCalculatorValues,
    type CalculatorEstimateBreakdown,
    type CalculatorField,
    type CalculatorOption,
    type CalculatorProfile,
    type CalculatorSelection,
    type CalculatorValues,
    type CompatibleCalculatorComponent,
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

function componentCategoryLabel(
    category: string,
    locale: string,
): string {
    const labels: Record<string, Record<string, string>> = {
        camera: { ka: "კამერა", en: "Camera", ru: "Камера" },
        recorder: { ka: "ჩამწერი", en: "Recorder", ru: "Регистратор" },
        storage: { ka: "საცავი", en: "Storage", ru: "Накопитель" },
        network: { ka: "ქსელი", en: "Network", ru: "Сеть" },
        cabling: { ka: "კაბელირება", en: "Cabling", ru: "Кабель" },
        accessory: { ka: "აქსესუარი", en: "Accessory", ru: "Аксессуар" },
        labor: { ka: "სამუშაო", en: "Labor", ru: "Работа" },
        power: { ka: "კვება", en: "Power", ru: "Питание" },
        lock: { ka: "საკეტი", en: "Lock", ru: "Замок" },
        intercom: { ka: "ინტერკომი", en: "Intercom", ru: "Домофон" },
        server: { ka: "სერვერი", en: "Server", ru: "Сервер" },
    };

    return labels[category]?.[locale] ?? labels[category]?.ka ?? category;
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
    const [componentSelectionsByService, setComponentSelectionsByService] =
        useState<Record<string, Record<string, CalculatorSelection>>>({});

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
    const compatibleComponents = useMemo(
        () =>
            profile
                ? getCompatibleComponents(
                      profile,
                      values,
                      projectSize,
                      propertyType,
                      packageKey,
                  )
                : [],
        [profile, values, projectSize, propertyType, packageKey],
    );
    const componentSelections = profile
        ? componentSelectionsByService[profile.slug] ?? {}
        : {};
    const totals = useMemo(
        () =>
            profile
                ? calculateConfiguratorTotals(
                      profile,
                      estimate,
                      compatibleComponents,
                      componentSelections,
                  )
                : {
                      serviceSubtotal: 0,
                      componentSubtotal: 0,
                      laborSubtotal: 0,
                      subtotalBeforeDiscount: 0,
                      discountPercentage: 0,
                      discountAmount: 0,
                      total: 0,
                      monthlyTotal: 0,
                  },
        [profile, estimate, compatibleComponents, componentSelections],
    );

    const copy = {
        eyebrow: t("calculator.embed.eyebrow", {
            ka: "საორიენტაციო ღირებულება",
            en: "Indicative pricing",
            ru: "Ориентировочная стоимость",
        }),
        title: t("calculator.embed.title", {
            ka: "სერვისების კალკულატორი და კონფიგურატორი",
            en: "Service calculator and configurator",
            ru: "Калькулятор и конфигуратор услуг",
        }),
        description: t("calculator.embed.description", {
            ka: "აირჩიეთ სერვისი და პარამეტრები. სისტემა ავტომატურად შემოგთავაზებთ თავსებად მოწყობილობებსა და სამუშაოებს.",
            en: "Choose a service and its parameters. The system automatically recommends compatible equipment and labor.",
            ru: "Выберите услугу и параметры. Система автоматически предложит совместимое оборудование и работы.",
        }),
        service: t("calculator.form.service", {
            ka: "მომსახურების ტიპი",
            en: "Service type",
            ru: "Тип услуги",
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
        required: t("calculator.component.required", {
            ka: "აუცილებელი",
            en: "Required",
            ru: "Обязательно",
        }),
        componentsTitle: t("calculator.components.title", {
            ka: "თავსებადი კომპონენტები და სამუშაოები",
            en: "Compatible components and labor",
            ru: "Совместимые компоненты и работы",
        }),
        componentsDescription: t("calculator.components.description", {
            ka: "რეკომენდაციები იცვლება არჩეული რაოდენობის, ტექნოლოგიისა და მახასიათებლების მიხედვით.",
            en: "Recommendations change according to quantity, technology and selected specifications.",
            ru: "Рекомендации меняются в зависимости от количества, технологии и характеристик.",
        }),
        quantity: t("calculator.components.quantity", {
            ka: "რაოდენობა",
            en: "Quantity",
            ru: "Количество",
        }),
        serviceSubtotal: t("calculator.summary.service", {
            ka: "მომსახურების საფასური",
            en: "Service price",
            ru: "Стоимость услуги",
        }),
        componentsSubtotal: t("calculator.summary.components", {
            ka: "კომპონენტების საფასური",
            en: "Component cost",
            ru: "Стоимость компонентов",
        }),
        laborSubtotal: t("calculator.summary.labor", {
            ka: "ფიქსირებული სამუშაოს საფასური",
            en: "Fixed labor fee",
            ru: "Фиксированная стоимость работ",
        }),
        subtotal: t("calculator.summary.subtotal", {
            ka: "ჯამი ფასდაკლებამდე",
            en: "Subtotal before discount",
            ru: "Сумма до скидки",
        }),
        discount: t("calculator.summary.discount", {
            ka: "ფასდაკლება",
            en: "Discount",
            ru: "Скидка",
        }),
        total: t("calculator.summary.total", {
            ka: "საორიენტაციო სრული ღირებულება",
            en: "Indicative total",
            ru: "Ориентировочная итоговая стоимость",
        }),
        monthly: t("calculator.summary.monthly", {
            ka: "ყოველთვიური მომსახურება",
            en: "Monthly service",
            ru: "Ежемесячное обслуживание",
        }),
        breakdown: t("calculator.summary.breakdown", {
            ka: "არჩეული კონფიგურაცია",
            en: "Selected configuration",
            ru: "Выбранная конфигурация",
        }),
        exactQuote: t("calculator.actions.contact", {
            ka: "ზუსტი შეთავაზების მოთხოვნა",
            en: "Request an exact quote",
            ru: "Запросить точное предложение",
        }),
        emptyComponents: t("calculator.components.empty", {
            ka: "ამ არჩევანისთვის თავსებადი კომპონენტი ჯერ არ არის დამატებული. ადმინისტრატორს შეუძლია კატალოგის შევსება.",
            en: "No compatible components are configured for this selection yet. The administrator can extend the catalog.",
            ru: "Для этого выбора пока не настроены совместимые компоненты. Администратор может дополнить каталог.",
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

    const updateComponentSelection = (
        recommendation: CompatibleCalculatorComponent,
        update: Partial<CalculatorSelection>,
    ) => {
        const { component, quantity } = recommendation;

        setComponentSelectionsByService((current) => {
            const serviceSelections = current[profile.slug] ?? {};
            const previous = serviceSelections[component.key];

            return {
                ...current,
                [profile.slug]: {
                    ...serviceSelections,
                    [component.key]: {
                        selected:
                            component.required ||
                            update.selected ??
                            previous?.selected ??
                            component.recommended,
                        quantity:
                            update.quantity ?? previous?.quantity ?? quantity,
                    },
                },
            };
        });
    };

    const isComponentSelected = (
        recommendation: CompatibleCalculatorComponent,
    ) => {
        const { component } = recommendation;
        const override = componentSelections[component.key];

        return component.required
            ? true
            : override?.selected ?? component.recommended;
    };

    const componentQuantity = (
        recommendation: CompatibleCalculatorComponent,
    ) =>
        componentSelections[recommendation.component.key]?.quantity ??
        recommendation.quantity;

    const selectedComponents = compatibleComponents.filter((recommendation) =>
        isComponentSelected(recommendation),
    );

    return (
        <section className="scroll-mt-28 pt-unit-2xl" id="service-calculator">
            <div className="overflow-hidden rounded-3xl border border-outline-variant/20 bg-surface-container/70 shadow-[0_24px_80px_rgba(15,23,42,0.08)]">
                <div className="border-b border-outline-variant/20 bg-primary/5 px-5 py-7 sm:px-8 sm:py-9">
                    <div className="inline-flex items-center gap-2 rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-primary">
                        <Icon className="text-base" name="calculate" />
                        {copy.eyebrow}
                    </div>
                    <h2 className="mt-4 max-w-4xl text-2xl font-semibold leading-tight text-on-surface sm:text-3xl">
                        {copy.title}
                    </h2>
                    <p className="mt-3 max-w-4xl text-sm leading-6 text-on-surface-variant sm:text-base">
                        {copy.description}
                    </p>
                </div>

                <div className="grid gap-8 p-5 sm:p-8 lg:grid-cols-[minmax(0,1fr)_25rem] lg:items-start">
                    <div className="space-y-9">
                        <div className="space-y-7 rounded-2xl border border-outline-variant/20 bg-surface p-5 sm:p-6">
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

                        <section className="rounded-2xl border border-outline-variant/20 bg-surface p-5 sm:p-6">
                            <div className="flex items-start gap-3">
                                <span className="mt-0.5 inline-flex size-10 shrink-0 items-center justify-center rounded-xl bg-secondary/10 text-secondary">
                                    <Icon name="inventory_2" />
                                </span>
                                <div>
                                    <h3 className="text-xl font-semibold text-on-surface">
                                        {copy.componentsTitle}
                                    </h3>
                                    <p className="mt-1 text-sm leading-6 text-on-surface-variant">
                                        {copy.componentsDescription}
                                    </p>
                                </div>
                            </div>

                            {compatibleComponents.length ? (
                                <div className="mt-6 grid gap-3">
                                    {compatibleComponents.map((recommendation) => {
                                        const { component } = recommendation;
                                        const selected =
                                            isComponentSelected(recommendation);
                                        const quantity =
                                            componentQuantity(recommendation);

                                        return (
                                            <article
                                                className={`rounded-2xl border p-4 transition-colors ${
                                                    selected
                                                        ? "border-primary/40 bg-primary/5"
                                                        : "border-outline-variant/20 bg-surface-container-low/50"
                                                }`}
                                                key={component.key}
                                            >
                                                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                                    <label className="flex min-w-0 cursor-pointer items-start gap-3">
                                                        <input
                                                            checked={selected}
                                                            className="mt-1 size-5 accent-primary"
                                                            disabled={component.required}
                                                            onChange={(event) =>
                                                                updateComponentSelection(
                                                                    recommendation,
                                                                    {
                                                                        selected:
                                                                            event.target
                                                                                .checked,
                                                                    },
                                                                )
                                                            }
                                                            type="checkbox"
                                                        />
                                                        <span className="min-w-0">
                                                            <span className="flex flex-wrap items-center gap-2">
                                                                <span className="font-semibold text-on-surface">
                                                                    {component.title}
                                                                </span>
                                                                <span className="rounded-full bg-surface-container-high px-2 py-1 text-[11px] text-on-surface-variant">
                                                                    {componentCategoryLabel(
                                                                        component.category,
                                                                        locale,
                                                                    )}
                                                                </span>
                                                                {component.required ? (
                                                                    <span className="rounded-full bg-error/10 px-2 py-1 text-[11px] text-error">
                                                                        {copy.required}
                                                                    </span>
                                                                ) : component.recommended ? (
                                                                    <span className="rounded-full bg-secondary/10 px-2 py-1 text-[11px] text-secondary">
                                                                        {copy.recommended}
                                                                    </span>
                                                                ) : null}
                                                            </span>
                                                            {component.description ? (
                                                                <span className="mt-1 block text-sm leading-5 text-on-surface-variant">
                                                                    {component.description}
                                                                </span>
                                                            ) : null}
                                                            <span className="mt-2 block text-sm font-medium text-primary">
                                                                {money(
                                                                    component.unitPrice,
                                                                    profile.currency,
                                                                    locale,
                                                                )}
                                                                {component.monthlyPrice > 0
                                                                    ? ` + ${money(
                                                                          component.monthlyPrice,
                                                                          profile.currency,
                                                                          locale,
                                                                      )} / ${copy.monthly}`
                                                                    : ""}
                                                            </span>
                                                        </span>
                                                    </label>

                                                    <label className="grid w-full shrink-0 gap-1 text-xs text-on-surface-variant sm:w-28">
                                                        <span>{copy.quantity}</span>
                                                        <input
                                                            className="min-h-11 rounded-xl border border-outline-variant/30 bg-surface px-3 text-on-surface outline-none focus:border-primary disabled:opacity-50"
                                                            disabled={!selected}
                                                            min={0}
                                                            onChange={(event) =>
                                                                updateComponentSelection(
                                                                    recommendation,
                                                                    {
                                                                        quantity: Math.max(
                                                                            0,
                                                                            Number(
                                                                                event.target
                                                                                    .value,
                                                                            ) || 0,
                                                                        ),
                                                                    },
                                                                )
                                                            }
                                                            step="1"
                                                            type="number"
                                                            value={quantity}
                                                        />
                                                    </label>
                                                </div>
                                            </article>
                                        );
                                    })}
                                </div>
                            ) : (
                                <p className="mt-6 rounded-xl border border-dashed border-outline-variant/40 px-4 py-6 text-sm leading-6 text-on-surface-variant">
                                    {copy.emptyComponents}
                                </p>
                            )}
                        </section>
                    </div>

                    <aside className="rounded-2xl border border-outline-variant/20 bg-surface p-5 lg:sticky lg:top-28">
                        <div className="flex items-center justify-between gap-4">
                            <span className="text-sm text-on-surface-variant">
                                {copy.total}
                            </span>
                            <Icon className="text-secondary" name="query_stats" />
                        </div>
                        <p className="mt-2 break-words text-4xl font-semibold text-on-surface">
                            {money(totals.total, profile.currency, locale)}
                        </p>

                        <dl className="mt-6 space-y-3 border-t border-outline-variant/20 pt-5 text-sm">
                            <div className="flex items-center justify-between gap-4">
                                <dt className="text-on-surface-variant">
                                    {copy.serviceSubtotal}
                                </dt>
                                <dd className="font-medium text-on-surface">
                                    {money(
                                        totals.serviceSubtotal,
                                        profile.currency,
                                        locale,
                                    )}
                                </dd>
                            </div>
                            <div className="flex items-center justify-between gap-4">
                                <dt className="text-on-surface-variant">
                                    {copy.componentsSubtotal}
                                </dt>
                                <dd className="font-medium text-on-surface">
                                    {money(
                                        totals.componentSubtotal,
                                        profile.currency,
                                        locale,
                                    )}
                                </dd>
                            </div>
                            {totals.laborSubtotal > 0 ? (
                                <div className="flex items-center justify-between gap-4">
                                    <dt className="text-on-surface-variant">
                                        {copy.laborSubtotal}
                                    </dt>
                                    <dd className="font-medium text-on-surface">
                                        {money(
                                            totals.laborSubtotal,
                                            profile.currency,
                                            locale,
                                        )}
                                    </dd>
                                </div>
                            ) : null}
                            <div className="flex items-center justify-between gap-4 border-t border-outline-variant/20 pt-3">
                                <dt className="text-on-surface-variant">
                                    {copy.subtotal}
                                </dt>
                                <dd className="font-medium text-on-surface">
                                    {money(
                                        totals.subtotalBeforeDiscount,
                                        profile.currency,
                                        locale,
                                    )}
                                </dd>
                            </div>
                            {totals.discountAmount > 0 ? (
                                <div className="flex items-center justify-between gap-4 rounded-xl bg-success/10 px-3 py-2 text-success">
                                    <dt>
                                        {copy.discount} {totals.discountPercentage}%
                                    </dt>
                                    <dd className="font-semibold">
                                        − {money(
                                            totals.discountAmount,
                                            profile.currency,
                                            locale,
                                        )}
                                    </dd>
                                </div>
                            ) : null}
                        </dl>

                        {totals.monthlyTotal > 0 ? (
                            <div className="mt-5 border-t border-outline-variant/20 pt-5">
                                <p className="text-sm text-on-surface-variant">
                                    {copy.monthly}
                                </p>
                                <p className="mt-1 text-2xl font-semibold text-secondary">
                                    {money(
                                        totals.monthlyTotal,
                                        profile.currency,
                                        locale,
                                    )}
                                </p>
                            </div>
                        ) : null}

                        {selectedComponents.length ? (
                            <div className="mt-6 border-t border-outline-variant/20 pt-5">
                                <p className="text-sm font-semibold text-on-surface">
                                    {copy.breakdown}
                                </p>
                                <div className="mt-4 space-y-3">
                                    {selectedComponents.map((recommendation) => {
                                        const quantity =
                                            componentQuantity(recommendation);
                                        const { component } = recommendation;

                                        return (
                                            <div
                                                className="rounded-xl border border-outline-variant/20 bg-surface-container-low p-3"
                                                key={component.key}
                                            >
                                                <div className="flex items-start justify-between gap-3">
                                                    <div className="min-w-0">
                                                        <p className="text-sm font-medium text-on-surface">
                                                            {component.title}
                                                        </p>
                                                        <p className="mt-1 text-xs text-on-surface-variant">
                                                            {quantity} × {money(
                                                                component.unitPrice,
                                                                profile.currency,
                                                                locale,
                                                            )}
                                                        </p>
                                                    </div>
                                                    <p className="text-right text-xs font-semibold text-primary">
                                                        {money(
                                                            component.unitPrice *
                                                                quantity,
                                                            profile.currency,
                                                            locale,
                                                        )}
                                                    </p>
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
