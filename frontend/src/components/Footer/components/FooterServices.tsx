import TranslatedText from "@/components/i18n/TranslatedText";
import LocalizedLink from "@/components/ui/LocalizedLink";
import Typography from "@/components/ui/Typography";
import { getBackendServices } from "@/lib/backend";

export default async function FooterServices() {
    const services = await getBackendServices();

    if (!services.length) return null;

    return (
        <nav aria-labelledby="footer-services-title" className="space-y-4">
            <Typography as="h2" id="footer-services-title" variant="footer-title">
                <TranslatedText
                    fallback={{ ka: "სერვისები", en: "Services", ru: "Услуги" }}
                    translationKey="footer.services.title"
                />
            </Typography>
            <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                {services.slice(0, 6).map((service) => (
                    <li key={service.slug}>
                        <LocalizedLink
                            className="inline-flex min-h-10 items-center transition-colors duration-300 hover:text-secondary"
                            href={`/services/${service.slug}`}
                            prefetch={false}
                        >
                            <TranslatedText
                                fallback={service.title}
                                translationKey={`service.${service.slug}.card.title`}
                            />
                        </LocalizedLink>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
