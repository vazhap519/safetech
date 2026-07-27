export type CalculatorOption = {
    value: string;
    label: string;
    oneTimePrice: number;
    monthlyPrice: number;
};

type CalculatorFieldType =
    | "text"
    | "number"
    | "textarea"
    | "select"
    | "checkbox";

export type CalculatorField = {
    key: string;
    type: CalculatorFieldType;
    required: boolean;
    label: string;
    placeholder: string;
    help: string;
    unit: string;
    min: number | null;
    max: number | null;
    step: number | null;
    defaultValue: string | number | boolean;
    unitPrice: number;
    monthlyUnitPrice: number;
    priceMultiplierField: string;
    options: CalculatorOption[];
};

type CalculatorPackage = {
    key: string;
    title: string;
    description: string;
    oneTimePrice: number;
    monthlyPrice: number;
    recommended: boolean;
};

export type CalculatorProfile = {
    serviceId: number;
    slug: string;
    name: string;
    description: string;
    icon: string;
    currency: string;
    basePrice: number;
    monthlyBasePrice: number;
    minimumPrice: number;
    projectSize: {
        label: string;
        options: CalculatorOption[];
    };
    propertyType: {
        label: string;
        options: CalculatorOption[];
    };
    fields: CalculatorField[];
    packages: CalculatorPackage[];
    disclaimer: string;
};

export type CalculatorValues = Record<string, string | number | boolean>;

export type CalculatorEstimate = {
    oneTime: number;
    monthly: number;
};

export type CalculatorEstimateLine = {
    key: string;
    label: string;
    detail: string;
    kind:
        | "base"
        | "projectSize"
        | "propertyType"
        | "package"
        | "field"
        | "minimum";
    oneTime: number;
    monthly: number;
};

export type CalculatorEstimateBreakdown = CalculatorEstimate & {
    lines: CalculatorEstimateLine[];
};

function roundMoney(value: number) {
    return Math.round(Math.max(0, value) * 100) / 100;
}

function findOption(options: CalculatorOption[], value: string) {
    return options.find((option) => option.value === value);
}

function shouldDisplayLine(line: CalculatorEstimateLine) {
    return Boolean(line.detail) || line.oneTime > 0 || line.monthly > 0;
}

export function initialCalculatorValues(profile: CalculatorProfile) {
    return Object.fromEntries(
        profile.fields.map((field) => [
            field.key,
            field.defaultValue !== "" && field.defaultValue !== null
                ? field.defaultValue
                : field.type === "checkbox"
                  ? false
                  : field.type === "select"
                    ? field.options[0]?.value ?? ""
                    : "",
        ]),
    ) satisfies CalculatorValues;
}

export function calculateEstimateBreakdown(
    profile: CalculatorProfile,
    values: CalculatorValues,
    projectSize: string,
    propertyType: string,
    packageKey: string,
): CalculatorEstimateBreakdown {
    let oneTime = profile.basePrice;
    let monthly = profile.monthlyBasePrice;
    const lines: CalculatorEstimateLine[] = [];

    if (profile.basePrice > 0 || profile.monthlyBasePrice > 0) {
        lines.push({
            key: "base",
            label: "Base service cost",
            detail: profile.name,
            kind: "base",
            oneTime: roundMoney(profile.basePrice),
            monthly: roundMoney(profile.monthlyBasePrice),
        });
    }

    const selectedProjectSize = findOption(profile.projectSize.options, projectSize);

    if (selectedProjectSize) {
        oneTime += selectedProjectSize.oneTimePrice;
        monthly += selectedProjectSize.monthlyPrice;
        const line = {
            key: `project-size:${selectedProjectSize.value}`,
            label: profile.projectSize.label,
            detail: selectedProjectSize.label,
            kind: "projectSize" as const,
            oneTime: roundMoney(selectedProjectSize.oneTimePrice),
            monthly: roundMoney(selectedProjectSize.monthlyPrice),
        };

        if (shouldDisplayLine(line)) {
            lines.push(line);
        }
    }

    const selectedPropertyType = findOption(
        profile.propertyType.options,
        propertyType,
    );

    if (selectedPropertyType) {
        oneTime += selectedPropertyType.oneTimePrice;
        monthly += selectedPropertyType.monthlyPrice;
        const line = {
            key: `property-type:${selectedPropertyType.value}`,
            label: profile.propertyType.label,
            detail: selectedPropertyType.label,
            kind: "propertyType" as const,
            oneTime: roundMoney(selectedPropertyType.oneTimePrice),
            monthly: roundMoney(selectedPropertyType.monthlyPrice),
        };

        if (shouldDisplayLine(line)) {
            lines.push(line);
        }
    }

    const selectedPackage = profile.packages.find(
        (item) => item.key === packageKey,
    );

    if (selectedPackage) {
        oneTime += selectedPackage.oneTimePrice;
        monthly += selectedPackage.monthlyPrice;
        const line = {
            key: `package:${selectedPackage.key}`,
            label: "Service package",
            detail: selectedPackage.title,
            kind: "package" as const,
            oneTime: roundMoney(selectedPackage.oneTimePrice),
            monthly: roundMoney(selectedPackage.monthlyPrice),
        };

        if (shouldDisplayLine(line)) {
            lines.push(line);
        }
    }

    for (const field of profile.fields) {
        const value = values[field.key];

        if (field.type === "number") {
            const rawQuantity = Number(value) || 0;
            const quantity = Math.min(
                field.max ?? Number.POSITIVE_INFINITY,
                Math.max(field.min ?? 0, rawQuantity),
            );

            if (quantity <= 0) {
                continue;
            }

            const lineOneTime = quantity * field.unitPrice;
            const lineMonthly = quantity * field.monthlyUnitPrice;
            oneTime += lineOneTime;
            monthly += lineMonthly;

            lines.push({
                key: `field:${field.key}`,
                label: field.label,
                detail: field.unit ? `${quantity} ${field.unit}` : String(quantity),
                kind: "field",
                oneTime: roundMoney(lineOneTime),
                monthly: roundMoney(lineMonthly),
            });

            continue;
        }

        if (field.type === "checkbox") {
            if (!Boolean(value)) {
                continue;
            }

            oneTime += field.unitPrice;
            monthly += field.monthlyUnitPrice;

            lines.push({
                key: `field:${field.key}`,
                label: field.label,
                detail: "",
                kind: "field",
                oneTime: roundMoney(field.unitPrice),
                monthly: roundMoney(field.monthlyUnitPrice),
            });

            continue;
        }

        if (field.type === "select") {
            const selectedOption = findOption(field.options, String(value ?? ""));

            if (!selectedOption) {
                continue;
            }

            const multiplier = field.priceMultiplierField
                ? Math.max(0, Number(values[field.priceMultiplierField]) || 0)
                : 1;
            const lineOneTime = selectedOption.oneTimePrice * multiplier;
            const lineMonthly = selectedOption.monthlyPrice * multiplier;
            oneTime += lineOneTime;
            monthly += lineMonthly;

            const detail = field.priceMultiplierField && multiplier > 1
                ? `${selectedOption.label} x ${multiplier}`
                : selectedOption.label;
            const line = {
                key: `field:${field.key}:${selectedOption.value}`,
                label: field.label,
                detail,
                kind: "field" as const,
                oneTime: roundMoney(lineOneTime),
                monthly: roundMoney(lineMonthly),
            };

            if (shouldDisplayLine(line)) {
                lines.push(line);
            }
        }
    }

    if (oneTime < profile.minimumPrice) {
        lines.push({
            key: "minimum-price-adjustment",
            label: "Minimum project budget",
            detail: "",
            kind: "minimum",
            oneTime: roundMoney(profile.minimumPrice - oneTime),
            monthly: 0,
        });

        oneTime = profile.minimumPrice;
    }

    return {
        oneTime: roundMoney(oneTime),
        monthly: roundMoney(monthly),
        lines,
    };
}

export function calculateEstimate(
    profile: CalculatorProfile,
    values: CalculatorValues,
    projectSize: string,
    propertyType: string,
    packageKey: string,
): CalculatorEstimate {
    const estimate = calculateEstimateBreakdown(
        profile,
        values,
        projectSize,
        propertyType,
        packageKey,
    );

    return {
        oneTime: estimate.oneTime,
        monthly: estimate.monthly,
    };
}
