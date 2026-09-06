import TranslatedText from "@/components/i18n/TranslatedText";
import Typography from "@/components/ui/Typography";
import { toEmailHref, toPhoneHref } from "@/lib/contact-links";

type FooterContactDetails = {
    phone: string;
    phones: string[];
    email: string;
    address: string;
};

export default function FooterContact({
    contact,
}: {
    contact: FooterContactDetails;
}) {
    const phoneNumbers = contact.phones.length
        ? contact.phones
        : contact.phone
          ? [contact.phone]
          : [];
    const items = [
        phoneNumbers.length
            ? {
                  key: "phone",
                  content: (
                      <div className="flex flex-wrap items-center gap-x-2 gap-y-1">
                          {phoneNumbers.map((phone, index) => (
                              <span className="inline-flex items-center gap-2" key={phone}>
                                  {index > 0 ? (
                                      <span aria-hidden="true" className="text-outline">
                                          /
                                      </span>
                                  ) : null}
                                  <a
                                      className="inline-flex min-h-9 items-center transition-colors hover:text-secondary"
                                      href={toPhoneHref(phone)}
                                  >
                                      {phone}
                                  </a>
                              </span>
                          ))}
                      </div>
                  ),
              }
            : null,
        contact.email
            ? {
                  key: "email",
                  content: (
                      <a
                          className="inline-flex min-h-9 items-center break-all transition-colors hover:text-secondary"
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
                <ul className="space-y-1 text-[15px] leading-7 text-on-surface-variant">
                    {items.map((item) => (
                        <li key={item.key}>{item.content}</li>
                    ))}
                </ul>
            </address>
        </div>
    );
}
