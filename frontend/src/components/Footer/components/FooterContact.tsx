import Typography from "@/components/ui/Typography";
import { toEmailHref, toPhoneHref } from "@/lib/contact-links";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FooterContact() {
    const { contact, locale, translations } = await getSiteSettings();
    const title = translateText(translations, "footer.contact.title", locale, null);
    const items = [
        contact.phone
            ? {
                  key: "phone",
                  content: (
                      <a
                          className="inline-flex min-h-10 items-center transition-colors hover:text-secondary"
                          href={toPhoneHref(contact.phone)}
                      >
                          {contact.phone}
                      </a>
                  ),
              }
            : null,
        contact.email
            ? {
                  key: "email",
                  content: (
                      <a
                          className="inline-flex min-h-10 items-center break-all transition-colors hover:text-secondary"
                          href={toEmailHref(contact.email)}
                      >
                          {contact.email}
                      </a>
                  ),
              }
            : null,
        contact.address
            ? {
                  key: "address",
                  content: contact.address,
              }
            : null,
    ].filter((item): item is NonNullable<typeof item> => Boolean(item));

    if (!items.length) return null;

    return (
        <div className="space-y-4">
            {title ? (
                <Typography as="h2" variant="footer-title">
                    {title}
                </Typography>
            ) : null}
            <address className="not-italic">
                <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                    {items.map((item) => (
                        <li key={item.key}>{item.content}</li>
                    ))}
                </ul>
            </address>
        </div>
    );
}
