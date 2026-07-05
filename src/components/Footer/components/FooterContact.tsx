import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

function toPhoneHref(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export default async function FooterContact() {
    const { contact, locale, translations } = await getSiteSettings();

    return (
        <div className="space-y-4">
            <Typography as="h2" variant="footer-title">
                {translateText(translations, "footer.contact.title", locale, {
                    ka: "კონტაქტი",
                    en: "Contact",
                    ru: "Контакты",
                })}
            </Typography>
            <address className="not-italic">
                <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                    <li>
                        <a
                            className="transition-colors hover:text-secondary"
                            href={toPhoneHref(contact.phone)}
                        >
                            {contact.phone}
                        </a>
                    </li>
                    <li>
                        <a
                            className="break-all transition-colors hover:text-secondary"
                            href={`mailto:${contact.email}`}
                        >
                            {contact.email}
                        </a>
                    </li>
                    <li>{contact.address}</li>
                </ul>
            </address>
        </div>
    );
}
