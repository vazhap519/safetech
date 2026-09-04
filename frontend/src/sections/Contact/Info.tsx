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
    const phoneLabel = translateText(
        translations,
        "contact.info.phone",
        locale,
        null,
    );
    const details = [
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
                    className="break-all text-base font-semibold text-on-surface transition-colors hover:text-primary focus-visible:text-primary"
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
                <p className="text-base font-semibold text-on-surface">
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
                <p className="text-base font-semibold text-on-surface">
                    {contact.hours}
                </p>
            ) : null,
        },
    ].filter((item) => item.content);

    if (!phoneNumbers.length && !details.length) return null;

    return (
        <section
            aria-label={phoneLabel || undefined}
            className="border-y border-outline-variant/15 bg-surface-container-lowest py-8 sm:py-10 lg:py-12"
        >
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <div className="space-y-3 sm:space-y-4">
                    {phoneNumbers.length ? (
                        <article className="relative overflow-hidden rounded-2xl border border-primary/20 bg-gradient-to-br from-primary-container/20 via-surface-container-low to-surface-container-lowest shadow-[0_18px_45px_rgba(0,0,0,0.22)]">
                            <div
                                aria-hidden="true"
                                className="absolute -right-14 -top-16 h-44 w-44 rounded-full bg-primary/10 blur-3xl"
                            />

                            <div className="relative flex flex-col gap-5 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between lg:gap-8">
                                <div className="flex items-center gap-4">
                                    <span
                                        aria-hidden="true"
                                        className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-secondary/30 bg-secondary/10 text-secondary shadow-[0_0_20px_rgba(76,215,246,0.12)]"
                                    >
                                        <Icon className="text-2xl" name="call" />
                                    </span>

                                    {phoneLabel ? (
                                        <div>
                                            <p className="text-xs font-semibold uppercase tracking-[0.12em] text-secondary">
                                                {phoneLabel}
                                            </p>
                                            <p className="mt-1 text-sm text-on-surface-variant">
                                                {locale === "ka"
                                                    ? "დაგვირეკეთ პირდაპირ"
                                                    : locale === "ru"
                                                      ? "Позвоните нам напрямую"
                                                      : "Call us directly"}
                                            </p>
                                        </div>
                                    ) : null}
                                </div>

                                <div
                                    className={`grid w-full gap-2 sm:gap-3 lg:w-auto lg:min-w-[25rem] ${
                                        phoneNumbers.length === 1
                                            ? "sm:grid-cols-1"
                                            : "sm:grid-cols-2"
                                    }`}
                                >
                                    {phoneNumbers.map((phone, index) => (
                                        <a
                                            aria-label={
                                                phoneLabel
                                                    ? `${phoneLabel}: ${phone}`
                                                    : phone
                                            }
                                            className="group flex min-h-14 items-center justify-between gap-3 rounded-xl border border-outline-variant/35 bg-background/60 px-4 py-3 text-base font-semibold text-on-surface transition duration-200 hover:-translate-y-0.5 hover:border-primary/60 hover:bg-primary-container hover:text-on-primary-container focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface-container-lowest sm:text-lg"
                                            href={toPhoneHref(phone)}
                                            key={`${phone}-${index}`}
                                        >
                                            <span className="whitespace-nowrap">{phone}</span>
                                            <Icon
                                                className="text-xl transition-transform duration-200 group-hover:translate-x-0.5"
                                                name="arrow_forward"
                                            />
                                        </a>
                                    ))}
                                </div>
                            </div>
                        </article>
                    ) : null}

                    {details.length ? (
                        <div className="grid gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
                            {details.map((item) => (
                                <article
                                    className="glass-panel flex min-h-28 items-start gap-3 rounded-xl p-4 sm:p-5"
                                    key={item.icon}
                                >
                                    <Icon
                                        className="mt-0.5 shrink-0 text-xl text-primary"
                                        name={item.icon}
                                    />
                                    <div className="min-w-0">
                                        {item.label ? (
                                            <p className="mb-1 text-xs font-semibold uppercase tracking-[0.1em] text-on-surface-variant">
                                                {item.label}
                                            </p>
                                        ) : null}
                                        {item.content}
                                    </div>
                                </article>
                            ))}
                        </div>
                    ) : null}
                </div>
            </div>
        </section>
    );
}
