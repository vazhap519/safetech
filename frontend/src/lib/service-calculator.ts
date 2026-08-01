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

export type CalculatorPackage = {
    key: string;
    title: string;
    description: string;
    oneTimePrice: number;
    monthlyPrice: number;
    recommended: boolean;
};

export type CalculatorCompatibilityRule = {
    field: string;
    operator:
        | "equals"
        | "not_equals"
        | "gte"
        | "lte"
        | "contains"
        | "truthy"
        | "falsy";
    value: string;
};

export type CalculatorComponent = {
    key: string;
    category: string;
    title: string;
    description: string;
    unitPrice: number;
    monthlyPrice: number;
    quantityMode: "fixed" | "field" | "ceil";
    quantityField: string;
    defaultQuantity: number;
    unitsPerComponent: number;
    minimumQuantity: number;
    maximumQuantity: number | null;
    required: boolean;
    recommended: boolean;
    exclusiveGroup: string;
    priority: number;
    rules: CalculatorCompatibilityRule[];
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
    laborPrice: number;
    discountPercentage: number;
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
    components: CalculatorComponent[];
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

export type CompatibleCalculatorComponent = {
    component: CalculatorComponent;
    quantity: number;
};

export type CalculatorSelection = {
    selected: boolean;
    quantity: number;
};

export type ConfiguratorTotals = {
    serviceSubtotal: number;
    componentSubtotal: number;
    laborSubtotal: number;
    subtotalBeforeDiscount: number;
    discountPercentage: number;
    discountAmount: number;
    total: number;
    monthlyTotal: number;
};

export function roundMoney(value: number) {
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

function compatibilityValue(
    values: CalculatorValues,
    projectSize: string,
    propertyType: string,
    packageKey: string,
    field: string,
) {
    if (field === "project_size") return projectSize;
    if (field === "property_type") return propertyType;
    if (field === "package") return packageKey;

    return values[field];
}

function booleanValue(value: unknown) {
    if (typeof value === "boolean") return value;
    if (typeof value === "number") return value !== 0;

    return ["1", "true", "yes", "on"].includes(
        String(value ?? "").trim().toLowerCase(),
    );
}

function ruleMatches(
    rule: CalculatorCompatibilityRule,
    values: CalculatorValues,
    projectSize: string,
    propertyType: string,
    packageKey: string,
) {
    const actual = compatibilityValue(
        values,
        projectSize,
        propertyType,
        packageKey,
        rule.field,
    );
    const actualString = String(actual ?? "").trim().toLowerCase();
    const expectedString = rule.value.trim().toLowerCase();

    switch (rule.operator) {
        case "not_equals":
            return actualString !== expectedString;
        case "gte":
            return Number(actual) >= Number(rule.value);
        case "lte":
            return Number(actual) <= Number(rule.value);
        case "contains":
            return actualString.includes(expectedString);
        case "truthy":
            return booleanValue(actual);
        case "falsy":
            return !booleanValue(actual);
        case "equals":
        default:
            return actualString === expectedString;
    }
}

function calculatedComponentQuantity(
    component: CalculatorComponent,
    values: CalculatorValues,
) {
    const source = Math.max(0, Number(values[component.quantityField]) || 0);
    let quantity = component.defaultQuantity || 1;

    if (component.quantityMode === "field") {
        quantity = source;
    } else if (component.quantityMode === "ceil") {
        quantity = Math.ceil(source / Math.max(1, component.unitsPerComponent));
    }

    quantity = Math.max(component.minimumQuantity || 0, quantity);

    if (component.maximumQuantity !== null) {
        quantity = Math.min(component.maximumQuantity, quantity);
    }

    return roundMoney(quantity);
}

export function getCompatibleComponents(
    profile: CalculatorProfile,
    values: CalculatorValues,
    projectSize: string,
    propertyType: string,
    packageKey: string,
): CompatibleCalculatorComponent[] {
    const compatible = profile.components
        .filter((component) =>
            component.rules.every((rule) =>
                ruleMatches(rule, values, projectSize, propertyType, packageKey),
            ),
        )
        .sort((left, right) => right.priority - left.priority);
    const usedExclusiveGroups = new Set<string>();

    return compatible
        .filter((component) => {
            if (!component.exclusiveGroup) return true;
            if (usedExclusiveGroups.has(component.exclusiveGroup)) return false;

            usedExclusiveGroups.add(component.exclusiveGroup);
            return true;
        })
        .map((component) => ({
            component,
            quantity: calculatedComponentQuantity(component, values),
        }))
        .filter(({ quantity }) => quantity > 0);
}

export function calculateConfiguratorTotals(
    profile: CalculatorProfile,
    estimate: CalculatorEstimateBreakdown,
    compatibleComponents: CompatibleCalculatorComponent[],
    selections: Record<string, CalculatorSelection>,
): ConfiguratorTotals {
    let componentSubtotal = 0;
    let componentMonthly = 0;

    for (const recommendation of compatibleComponents) {
        const { component } = recommendation;
        const override = selections[component.key];
        const selected = component.required
            ? true
            : override?.selected ?? component.recommended;
        const quantity = Math.max(
            0,
            override?.quantity ?? recommendation.quantity,
        );

        if (!selected || quantity <= 0) continue;

        componentSubtotal += component.unitPrice * quantity;
        componentMonthly += component.monthlyPrice * quantity;
    }

    const serviceSubtotal = estimate.oneTime;
    const laborSubtotal = profile.laborPrice;
    const subtotalBeforeDiscount = roundMoney(
        serviceSubtotal + componentSubtotal + laborSubtotal,
    );
    const discountPercentage = Math.min(
        100,
        Math.max(0, profile.discountPercentage),
    );
    const discountAmount = roundMoney(
        subtotalBeforeDiscount * (discountPercentage / 100),
    );

    return {
        serviceSubtotal: roundMoney(serviceSubtotal),
        componentSubtotal: roundMoney(componentSubtotal),
        laborSubtotal: roundMoney(laborSubtotal),
        subtotalBeforeDiscount,
        discountPercentage: roundMoney(discountPercentage),
        discountAmount,
        total: roundMoney(subtotalBeforeDiscount - discountAmount),
        monthlyTotal: roundMoney(estimate.monthly + componentMonthly),
    };
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

        if (shouldDisplayLine(line)) lines.push(line);
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

        if (shouldDisplayLine(line)) lines.push(line);
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

        if (shouldDisplayLine(line)) lines.push(line);
    }

    for (const field of profile.fields) {
        const value = values[field.key];

        if (field.type === "number") {
            const rawQuantity = Number(value) || 0;
            const quantity = Math.min(
                field.max ?? Number.POSITIVE_INFINITY,
                Math.max(field.min ?? 0, rawQuantity),
            );

            if (quantity <= 0) continue;

            const lineOneTime = quantity * field.unitPrice;
            const lineMonthly = quantity * field.monthlyUnitPrice;
            oneTime += lineOneTime;
            monthly += lineMonthly;

            if (lineOneTime > 0 || lineMonthly > 0) {
                lines.push({
                    key: `field:${field.key}`,
                    label: field.label,
                    detail: field.unit ? `${quantity} ${field.unit}` : String(quantity),
                    kind: "field",
                    oneTime: roundMoney(lineOneTime),
                    monthly: roundMoney(lineMonthly),
                });
            }

            continue;
        }

        if (field.type === "checkbox") {
            if (!Boolean(value)) continue;

            oneTime += field.unitPrice;
            monthly += field.monthlyUnitPrice;

            if (field.unitPrice > 0 || field.monthlyUnitPrice > 0) {
                lines.push({
                    key: `field:${field.key}`,
                    label: field.label,
                    detail: "",
                    kind: "field",
                    oneTime: roundMoney(field.unitPrice),
                    monthly: roundMoney(field.monthlyUnitPrice),
                });
            }

            continue;
        }

        if (field.type === "select") {
            const selectedOption = findOption(field.options, String(value ?? ""));
            if (!selectedOption) continue;

            const multiplier = field.priceMultiplierField
                ? Math.max(0, Number(values[field.priceMultiplierField]) || 0)
                : 1;
            const lineOneTime = selectedOption.oneTimePrice * multiplier;
            const lineMonthly = selectedOption.monthlyPrice * multiplier;
            oneTime += lineOneTime;
            monthly += lineMonthly;

            const detail = field.priceMultiplierField && multiplier > 1
                ? `${selectedOption.label} × ${multiplier}`
                : selectedOption.label;
            const line = {
                key: `field:${field.key}:${selectedOption.value}`,
                label: field.label,
                detail,
                kind: "field" as const,
                oneTime: roundMoney(lineOneTime),
                monthly: roundMoney(lineMonthly),
            };

            if (shouldDisplayLine(line)) lines.push(line);
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
