import Icon from "@/components/ui/Icon";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

function toPhoneHref(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export default async function Info() {
    const { contact, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);
    const items = [
        {
            icon: "call",
            label: t("contact.info.phone", {
                ka: "ტელეფონი",
                en: "Phone",
                ru: "Телефон",
            }),
            value: contact.phone,
            href: toPhoneHref(contact.phone),
            valueClassName: "break-words",
        },
        {
            icon: "mail",
            label: t("contact.info.email", {
                ka: "ელფოსტა",
                en: "Email",
                ru: "Email",
            }),
            value: contact.email,
            href: `mailto:${contact.email}`,
            valueClassName: "break-all",
        },
        {
            icon: "location_on",
            label: t("contact.info.address", {
                ka: "მისამართი",
                en: "Address",
                ru: "Адрес",
            }),
            value: contact.address,
        },
        {
            icon: "schedule",
            label: t("contact.info.hours", {
                ka: "სამუშაო საათები",
                en: "Working hours",
                ru: "Часы работы",
            }),
            value: contact.hours,
        },
    ];

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 gap-unit-md px-margin-desktop sm:grid-cols-2 lg:grid-cols-4">
                {items.map((item) => (
                    <div
                        key={item.label}
                        className="glass-panel rounded-xl p-unit-md text-center md:p-unit-lg"
                    >
                        <Icon
                            className="mb-4 block text-2xl text-primary md:text-3xl"
                            name={item.icon}
                        />

                        <p className="mb-2 text-xs font-label-md uppercase tracking-tighter text-on-surface-variant md:text-label-md">
                            {item.label}
                        </p>

                        {item.href ? (
                            <a
                                className={`font-headline-md text-base text-on-surface transition-colors hover:text-primary md:text-headline-md ${item.valueClassName ?? ""}`}
                                href={item.href}
                            >
                                {item.value}
                            </a>
                        ) : (
                            <p
                                className={`font-headline-md text-base text-on-surface md:text-headline-md ${item.valueClassName ?? ""}`}
                            >
                                {item.value}
                            </p>
                        )}
                    </div>
                ))}
            </div>
        </section>
    );
}
