import Icon from "@/components/ui/Icon";
import { toEmailHref, toPhoneHref } from "@/lib/contact-links";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Info() {
    const { contact, locale, translations } = await getSiteSettings();
    const items = [
        {
            icon: "call",
            label: translateText(
                translations,
                "contact.info.phone",
                locale,
                null,
            ),
            value: contact.phone,
            href: contact.phone ? toPhoneHref(contact.phone) : "",
            valueClassName: "break-words",
        },
        {
            icon: "mail",
            label: translateText(
                translations,
                "contact.info.email",
                locale,
                null,
            ),
            value: contact.email,
            href: contact.email ? toEmailHref(contact.email) : "",
            valueClassName: "break-all",
        },
        {
            icon: "location_on",
            label: translateText(
                translations,
                "contact.info.address",
                locale,
                null,
            ),
            value: contact.address,
        },
        {
            icon: "schedule",
            label: translateText(
                translations,
                "contact.info.hours",
                locale,
                null,
            ),
            value: contact.hours,
        },
    ].filter((item) => item.value);

    if (!items.length) return null;

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 gap-unit-md px-margin-desktop sm:grid-cols-2 lg:grid-cols-4">
                {items.map((item) => (
                    <div
                        key={`${item.icon}-${item.value}`}
                        className="glass-panel rounded-xl p-unit-md text-center md:p-unit-lg"
                    >
                        <Icon
                            className="mb-4 block text-2xl text-primary md:text-3xl"
                            name={item.icon}
                        />

                        {item.label ? (
                            <p className="mb-2 text-xs font-label-md uppercase tracking-tighter text-on-surface-variant md:text-label-md">
                                {item.label}
                            </p>
                        ) : null}

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
