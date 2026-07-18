export function toPhoneHref(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export function toEmailHref(email: string) {
    return `mailto:${email.trim()}`;
}
