import Icon from "@/components/ui/Icon";
import { toEmailHref, toPhoneHref } from "@/lib/contact-links";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Info() {
    const { contact, locale, translations } = await getSiteSettings();
    const phoneNumbers = contact.phones.length
        ? contact.phones
        : contact.phone
          ? [contact.phone]
          : [];
    const items = [
        {
            icon: "call",
            label: translateText(
                translations,
                "contact.info.phone",
                locale,
                null,
            ),
            content: phoneNumbers.length ? (
                <div className="flex flex-col items-center gap-1">
                    {phoneNumbers.map((phone, index) => (
                        <span className="contents" key={phone}>
                            <a
                                className="font-headline-md break-words text-base text-on-surface transition-colors hover:text-primary md:text-headline-md"
                                href={toPhoneHref(phone)}
                            >
                                {phone}
                            </a>
                            {index < phoneNumbers.length - 1 ? (
                                <span className="sr-only"> / </span>
                            ) : null}
                        </span>
                    ))}
                </div>
            ) : null,
        },
        {
            icon: "mail",
            label: translateText(
                translations,
                "contact.info.email",
                locale,
                null,
            ),
            content: contact.email ? (
                <a
                    className="font-headline-md break-all text-base text-on-surface transition-colors hover:text-primary md:text-headline-md"
                    href={toEmailHref(contact.email)}
                >
                    {contact.email}
                </a>
            ) : null,
        },
        {
            icon: "location_on",
            label: translateText(
                translations,
                "contact.info.address",
                locale,
                null,
            ),
            content: contact.address ? (
                <p className="font-headline-md text-base text-on-surface md:text-headline-md">
                    {contact.address}
                </p>
            ) : null,
        },
        {
            icon: "schedule",
            label: translateText(
                translations,
                "contact.info.hours",
                locale,
                null,
            ),
            content: contact.hours ? (
                <p className="font-headline-md text-base text-on-surface md:text-headline-md">
                    {contact.hours}
                </p>
            ) : null,
        },
    ].filter((item) => item.content);

    if (!items.length) return null;

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 gap-unit-md px-margin-desktop sm:grid-cols-2 lg:grid-cols-4">
                {items.map((item) => (
                    <div
                        key={item.icon}
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

                        {item.content}
                    </div>
                ))}
            </div>
        </section>
    );
}
