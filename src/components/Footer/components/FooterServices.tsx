import LocalizedLink from "@/components/ui/LocalizedLink";
import Typography from "@/components/ui/Typography";
import { getBackendServices } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FooterServices() {
    const [{ locale, translations }, services] = await Promise.all([
        getSiteSettings(),
        getBackendServices(),
    ]);

    if (!services.length) return null;

    return (
        <nav aria-label="სერვისები" className="space-y-4">
            <Typography as="h2" variant="footer-title">
                {translateText(translations, "footer.services.title", locale, {
                    ka: "სერვისები",
                    en: "Services",
                    ru: "Услуги",
                })}
            </Typography>
            <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                {services.slice(0, 6).map((service) => (
                    <li key={service.slug}>
                        <LocalizedLink
                            className="inline-flex min-h-10 items-center transition-colors duration-300 hover:text-secondary"
                            href={`/services/${service.slug}`}
                        >
                            {service.title}
                        </LocalizedLink>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
