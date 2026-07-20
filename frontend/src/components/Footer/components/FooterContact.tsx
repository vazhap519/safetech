import TranslatedText from "@/components/i18n/TranslatedText";
import Typography from "@/components/ui/Typography";
import { toEmailHref, toPhoneHref } from "@/lib/contact-links";
import { getSiteSettings } from "@/lib/site-settings";

export default async function FooterContact() {
    const { contact } = await getSiteSettings();
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
                  content: (
                      <TranslatedText
                          fallback={contact.address}
                          translationKey="footer.contact.address"
                      />
                  ),
              }
            : null,
    ].filter((item): item is NonNullable<typeof item> => Boolean(item));

    if (!items.length) return null;

    return (
        <div aria-labelledby="footer-contact-title" className="space-y-4">
            <Typography as="h2" id="footer-contact-title" variant="footer-title">
                <TranslatedText
                    fallback={{ ka: "კონტაქტი", en: "Contact", ru: "Контакты" }}
                    translationKey="footer.contact.title"
                />
            </Typography>
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
