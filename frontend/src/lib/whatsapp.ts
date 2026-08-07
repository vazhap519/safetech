export function normalizeWhatsAppPhone(value?: string | null): string {
    return value?.replace(/[^\d]/g, "") || "";
}

export function buildWhatsAppUrl(
    phone?: string | null,
    message?: string | null,
): string {
    const normalizedPhone = normalizeWhatsAppPhone(phone);

    if (!normalizedPhone) return "";

    const text = typeof message === "string" ? message : "";

    return `https://wa.me/${normalizedPhone}${
        text ? `?text=${encodeURIComponent(text)}` : ""
    }`;
}
