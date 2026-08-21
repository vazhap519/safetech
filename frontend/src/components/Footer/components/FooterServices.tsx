import TranslatedText from "@/components/i18n/TranslatedText";
import Typography from "@/components/ui/Typography";
import FooterServicesList from "@/components/Footer/components/FooterServicesList";
import { getBackendFooterServices } from "@/lib/backend";

export default async function FooterServices() {
    const services = await getBackendFooterServices();

    if (!services.length) return null;

    return (
        <nav aria-labelledby="footer-services-title" className="space-y-4">
            <Typography as="h2" id="footer-services-title" variant="footer-title">
                <TranslatedText
                    fallback={{ ka: "სერვისები", en: "Services", ru: "Услуги" }}
                    translationKey="footer.services.title"
                />
            </Typography>
            <FooterServicesList services={services.slice(0, 6)} />
        </nav>
    );
}
