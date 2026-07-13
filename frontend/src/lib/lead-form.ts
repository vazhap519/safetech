import type { Locale } from "@/lib/locales";

export type LeadFormFieldType = "text" | "number" | "textarea" | "select";

export type RawLeadFormOption = {
    value: string;
    ka?: string;
    en?: string;
    ru?: string;
};

export type RawLeadFormExtraField = {
    key: string;
    type?: LeadFormFieldType;
    required?: boolean;
    ka?: string;
    en?: string;
    ru?: string;
    placeholder_ka?: string;
    placeholder_en?: string;
    placeholder_ru?: string;
    options?: RawLeadFormOption[];
};

export type RawLeadFormConfig = {
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

export type LeadFormOption = {
    value: string;
    label: string;
};

export type LeadFormField = {
    key: string;
    type: LeadFormFieldType;
    required: boolean;
    label: string;
    placeholder: string;
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
    values: {
        ka?: string;
        en?: string;
        ru?: string;
    },
    fallback: string,
) {
    const value =
        values[locale] ??
        values.ka ??
        values.en ??
        values.ru ??
        fallback;

    return value?.trim() || fallback;
}

const genericLeadForm: RawLeadFormConfig = {
    project_size_label_ka: "პროექტის ზომა",
    project_size_label_en: "Project size",
    project_size_label_ru: "Размер проекта",
    project_size_options: [
        { value: "small", ka: "მცირე", en: "Small", ru: "Малый" },
        { value: "medium", ka: "საშუალო", en: "Medium", ru: "Средний" },
        { value: "large", ka: "დიდი", en: "Large", ru: "Крупный" },
    ],
    property_type_label_ka: "ობიექტის ტიპი",
    property_type_label_en: "Property type",
    property_type_label_ru: "Тип объекта",
    property_type_options: [
        { value: "office", ka: "ოფისი", en: "Office", ru: "Офис" },
        { value: "warehouse", ka: "საწყობი", en: "Warehouse", ru: "Склад" },
        { value: "retail", ka: "მაღაზია", en: "Retail", ru: "Магазин" },
    ],
    extra_fields: [],
};

const defaultLeadFormsBySlug: Record<string, RawLeadFormConfig> = {
    cctv: {
        project_size_label_ka: "კამერების მასშტაბი",
        project_size_label_en: "Camera scale",
        project_size_label_ru: "Масштаб камер",
        project_size_options: [
            { value: "small", ka: "1-16 კამერა", en: "1-16 cameras", ru: "1-16 камер" },
            { value: "medium", ka: "16-64 კამერა", en: "16-64 cameras", ru: "16-64 камеры" },
            { value: "large", ka: "64+ კამერა", en: "64+ cameras", ru: "64+ камер" },
        ],
        property_type_label_ka: "ობიექტის ტიპი",
        property_type_label_en: "Property type",
        property_type_label_ru: "Тип объекта",
        property_type_options: [
            { value: "office", ka: "ოფისი", en: "Office", ru: "Офис" },
            { value: "warehouse", ka: "საწყობი", en: "Warehouse", ru: "Склад" },
            { value: "retail", ka: "მაღაზია", en: "Retail", ru: "Магазин" },
        ],
        extra_fields: [
            {
                key: "camera_count",
                type: "number",
                required: true,
                ka: "კამერების რაოდენობა",
                en: "Number of cameras",
                ru: "Количество камер",
                placeholder_ka: "მაგ: 24",
                placeholder_en: "Example: 24",
                placeholder_ru: "Например: 24",
            },
            {
                key: "recording_days",
                type: "number",
                required: false,
                ka: "ჩანაწერის დღეები",
                en: "Recording days",
                ru: "Дней хранения",
                placeholder_ka: "მაგ: 30",
                placeholder_en: "Example: 30",
                placeholder_ru: "Например: 30",
            },
        ],
    },
    "access-control": {
        project_size_label_ka: "წვდომის წერტილების მასშტაბი",
        project_size_label_en: "Access scale",
        project_size_label_ru: "Масштаб точек доступа",
        project_size_options: [
            { value: "small", ka: "1-5 კარი", en: "1-5 doors", ru: "1-5 дверей" },
            { value: "medium", ka: "6-20 კარი", en: "6-20 doors", ru: "6-20 дверей" },
            { value: "large", ka: "20+ კარი", en: "20+ doors", ru: "20+ дверей" },
        ],
        property_type_label_ka: "ობიექტის ტიპი",
        property_type_label_en: "Property type",
        property_type_label_ru: "Тип объекта",
        property_type_options: [
            { value: "office", ka: "ოფისი", en: "Office", ru: "Офис" },
            { value: "clinic", ka: "კლინიკა", en: "Clinic", ru: "Клиника" },
            { value: "hotel", ka: "სასტუმრო", en: "Hotel", ru: "Отель" },
        ],
        extra_fields: [
            {
                key: "door_count",
                type: "number",
                required: true,
                ka: "კარების რაოდენობა",
                en: "Door count",
                ru: "Количество дверей",
                placeholder_ka: "მაგ: 8",
                placeholder_en: "Example: 8",
                placeholder_ru: "Например: 8",
            },
        ],
    },
    networking: {
        project_size_label_ka: "ქსელის მასშტაბი",
        project_size_label_en: "Network size",
        project_size_label_ru: "Размер сети",
        project_size_options: [
            { value: "small", ka: "პატარა ობიექტი", en: "Small site", ru: "Малый объект" },
            { value: "medium", ka: "საშუალო ობიექტი", en: "Medium site", ru: "Средний объект" },
            { value: "large", ka: "დიდი ობიექტი", en: "Large site", ru: "Крупный объект" },
        ],
        property_type_label_ka: "ქსელის გარემო",
        property_type_label_en: "Network environment",
        property_type_label_ru: "Тип сети",
        property_type_options: [
            { value: "office", ka: "ოფისი", en: "Office", ru: "Офис" },
            { value: "hotel", ka: "სასტუმრო", en: "Hotel", ru: "Отель" },
            { value: "warehouse", ka: "საწყობი", en: "Warehouse", ru: "Склад" },
        ],
        extra_fields: [
            {
                key: "router_count",
                type: "number",
                required: false,
                ka: "როუტერების რაოდენობა",
                en: "Router count",
                ru: "Количество роутеров",
                placeholder_ka: "მაგ: 4",
                placeholder_en: "Example: 4",
                placeholder_ru: "Например: 4",
            },
            {
                key: "switch_count",
                type: "number",
                required: false,
                ka: "სვიჩების რაოდენობა",
                en: "Switch count",
                ru: "Количество свичей",
                placeholder_ka: "მაგ: 12",
                placeholder_en: "Example: 12",
                placeholder_ru: "Например: 12",
            },
            {
                key: "access_point_count",
                type: "number",
                required: false,
                ka: "Access Point-ების რაოდენობა",
                en: "Access point count",
                ru: "Количество точек доступа",
                placeholder_ka: "მაგ: 18",
                placeholder_en: "Example: 18",
                placeholder_ru: "Например: 18",
            },
        ],
    },
    "server-infrastructure": {
        project_size_label_ka: "ინფრასტრუქტურის მასშტაბი",
        project_size_label_en: "Infrastructure size",
        project_size_label_ru: "Размер инфраструктуры",
        project_size_options: [
            { value: "small", ka: "პატარა გარემო", en: "Small environment", ru: "Малая среда" },
            { value: "medium", ka: "საშუალო გარემო", en: "Medium environment", ru: "Средняя среда" },
            { value: "large", ka: "დიდი გარემო", en: "Large environment", ru: "Крупная среда" },
        ],
        property_type_label_ka: "ინფრასტრუქტურის ტიპი",
        property_type_label_en: "Infrastructure type",
        property_type_label_ru: "Тип инфраструктуры",
        property_type_options: [
            { value: "onprem", ka: "ადგილობრივი", en: "On-premise", ru: "Локальная" },
            { value: "hybrid", ka: "ჰიბრიდული", en: "Hybrid", ru: "Гибридная" },
            { value: "datacenter", ka: "დატაცენტრი", en: "Data center", ru: "Дата-центр" },
        ],
        extra_fields: [
            {
                key: "server_count",
                type: "number",
                required: false,
                ka: "სერვერების რაოდენობა",
                en: "Server count",
                ru: "Количество серверов",
                placeholder_ka: "მაგ: 6",
                placeholder_en: "Example: 6",
                placeholder_ru: "Например: 6",
            },
        ],
    },
};

export function getDefaultLeadFormConfig(slug?: string | null) {
    if (!slug) {
        return genericLeadForm;
    }

    return defaultLeadFormsBySlug[slug] ?? genericLeadForm;
}

function localizeOptions(
    locale: Locale,
    options: RawLeadFormOption[] | undefined,
) {
    return (options ?? []).map((option) => ({
        value: option.value,
        label: pickLocalizedValue(locale, option, option.value),
    }));
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
        extraFields: (resolvedConfig.extra_fields ?? []).map((field) => ({
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
            options: localizeOptions(locale, field.options),
        })),
    };
}
