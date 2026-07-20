import type { Locale } from "@/lib/locales";

type LeadFormFieldType =
    | "text"
    | "number"
    | "textarea"
    | "select"
    | "checkbox";

type RawLeadFormOption = {
    value: string;
    ka?: string;
    en?: string;
    ru?: string;
    one_time_price?: number;
    monthly_price?: number;
};

type RawLeadFormExtraField = {
    key: string;
    type?: LeadFormFieldType;
    required?: boolean;
    ka?: string;
    en?: string;
    ru?: string;
    placeholder_ka?: string;
    placeholder_en?: string;
    placeholder_ru?: string;
    help_ka?: string;
    help_en?: string;
    help_ru?: string;
    unit_ka?: string;
    unit_en?: string;
    unit_ru?: string;
    min?: number;
    max?: number;
    step?: number;
    default?: string | number | boolean;
    unit_price?: number;
    monthly_unit_price?: number;
    options?: RawLeadFormOption[];
};

export type RawLeadFormConfig = {
    calculator_enabled?: boolean;
    project_size_label_ka?: string;
    project_size_label_en?: string;
    project_size_label_ru?: string;
    project_size_options?: RawLeadFormOption[];
    property_type_label_ka?: string;
    property_type_label_en?: string;
    property_type_label_ru?: string;
    property_type_options?: RawLeadFormOption[];
    extra_fields?: RawLeadFormExtraField[];
};

type LeadFormOption = {
    value: string;
    label: string;
};

export type LeadFormField = {
    key: string;
    type: LeadFormFieldType;
    required: boolean;
    label: string;
    placeholder: string;
    help: string;
    unit: string;
    min?: number;
    max?: number;
    step?: number;
    defaultValue?: string | number | boolean;
    options: LeadFormOption[];
};

export type LocalizedLeadFormConfig = {
    projectSizeLabel: string;
    projectSizeOptions: LeadFormOption[];
    propertyTypeLabel: string;
    propertyTypeOptions: LeadFormOption[];
    extraFields: LeadFormField[];
};

export type ContactServiceOption = {
    slug: string;
    label: string;
    leadForm: LocalizedLeadFormConfig;
};

function pickLocalizedValue(
    locale: Locale,
    values: { ka?: string; en?: string; ru?: string },
    fallback: string,
) {
    const value =
        values[locale] ?? values.ka ?? values.en ?? values.ru ?? fallback;

    return value?.trim() || fallback;
}

const genericLeadForm: RawLeadFormConfig = {
    project_size_label_ka: "პროექტის ზომა",
    project_size_label_en: "Project size",
    project_size_label_ru: "Размер проекта",
    project_size_options: [],
    property_type_label_ka: "ობიექტის ტიპი",
    property_type_label_en: "Property type",
    property_type_label_ru: "Тип объекта",
    property_type_options: [],
    extra_fields: [],
};

function localizeOptions(
    locale: Locale,
    options: RawLeadFormOption[] | undefined,
) {
    return (options ?? [])
        .filter((option) => option.value)
        .map((option) => ({
            value: option.value,
            label: pickLocalizedValue(locale, option, option.value),
        }));
}

function optionalNumber(value: unknown) {
    return typeof value === "number" && Number.isFinite(value)
        ? value
        : undefined;
}

export function localizeLeadFormConfig(
    config: RawLeadFormConfig | null | undefined,
    locale: Locale,
): LocalizedLeadFormConfig {
    const resolvedConfig = config ?? genericLeadForm;

    return {
        projectSizeLabel: pickLocalizedValue(
            locale,
            {
                ka: resolvedConfig.project_size_label_ka,
                en: resolvedConfig.project_size_label_en,
                ru: resolvedConfig.project_size_label_ru,
            },
            "Project size",
        ),
        projectSizeOptions: localizeOptions(
            locale,
            resolvedConfig.project_size_options,
        ),
        propertyTypeLabel: pickLocalizedValue(
            locale,
            {
                ka: resolvedConfig.property_type_label_ka,
                en: resolvedConfig.property_type_label_en,
                ru: resolvedConfig.property_type_label_ru,
            },
            "Property type",
        ),
        propertyTypeOptions: localizeOptions(
            locale,
            resolvedConfig.property_type_options,
        ),
        extraFields: (resolvedConfig.extra_fields ?? [])
            .filter((field) => field.key)
            .map((field) => ({
                key: field.key,
                type: field.type ?? "text",
                required: Boolean(field.required),
                label: pickLocalizedValue(locale, field, field.key),
                placeholder: pickLocalizedValue(
                    locale,
                    {
                        ka: field.placeholder_ka,
                        en: field.placeholder_en,
                        ru: field.placeholder_ru,
                    },
                    "",
                ),
                help: pickLocalizedValue(
                    locale,
                    {
                        ka: field.help_ka,
                        en: field.help_en,
                        ru: field.help_ru,
                    },
                    "",
                ),
                unit: pickLocalizedValue(
                    locale,
                    {
                        ka: field.unit_ka,
                        en: field.unit_en,
                        ru: field.unit_ru,
                    },
                    "",
                ),
                min: optionalNumber(field.min),
                max: optionalNumber(field.max),
                step: optionalNumber(field.step),
                defaultValue: field.default,
                options: localizeOptions(locale, field.options),
            })),
    };
}
