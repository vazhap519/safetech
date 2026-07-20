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

function optionPrice(options: CalculatorOption[], value: string) {
    const selected = options.find((option) => option.value === value);

    return {
        oneTime: selected?.oneTimePrice ?? 0,
        monthly: selected?.monthlyPrice ?? 0,
    };
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

export function calculateEstimate(
    profile: CalculatorProfile,
    values: CalculatorValues,
    projectSize: string,
    propertyType: string,
    packageKey: string,
): CalculatorEstimate {
    let oneTime = profile.basePrice;
    let monthly = profile.monthlyBasePrice;

    for (const price of [
        optionPrice(profile.projectSize.options, projectSize),
        optionPrice(profile.propertyType.options, propertyType),
    ]) {
        oneTime += price.oneTime;
        monthly += price.monthly;
    }

    const selectedPackage = profile.packages.find(
        (item) => item.key === packageKey,
    );
    oneTime += selectedPackage?.oneTimePrice ?? 0;
    monthly += selectedPackage?.monthlyPrice ?? 0;

    for (const field of profile.fields) {
        const value = values[field.key];

        if (field.type === "number") {
            const rawQuantity = Number(value) || 0;
            const quantity = Math.min(
                field.max ?? Number.POSITIVE_INFINITY,
                Math.max(field.min ?? 0, rawQuantity),
            );
            oneTime += quantity * field.unitPrice;
            monthly += quantity * field.monthlyUnitPrice;
            continue;
        }

        if (field.type === "checkbox") {
            if (Boolean(value)) {
                oneTime += field.unitPrice;
                monthly += field.monthlyUnitPrice;
            }
            continue;
        }

        if (field.type === "select") {
            const price = optionPrice(field.options, String(value ?? ""));
            const multiplier = field.priceMultiplierField
                ? Math.max(0, Number(values[field.priceMultiplierField]) || 0)
                : 1;
            oneTime += price.oneTime * multiplier;
            monthly += price.monthly * multiplier;
        }
    }

    return {
        oneTime: Math.round(Math.max(profile.minimumPrice, oneTime) * 100) / 100,
        monthly: Math.round(Math.max(0, monthly) * 100) / 100,
    };
}
