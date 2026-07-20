import TranslatedText from "@/components/i18n/TranslatedText";
import LocalizedLink from "@/components/ui/LocalizedLink";
import Typography from "@/components/ui/Typography";
import { primaryNavigation } from "@/lib/navigation";

const companyLinks = primaryNavigation.filter((item) => item.href !== "/");

export default function FooterCompany() {
    return (
        <nav aria-labelledby="footer-company-title" className="space-y-4">
            <Typography as="h2" id="footer-company-title" variant="footer-title">
                <TranslatedText
                    fallback={{ ka: "კომპანია", en: "Company", ru: "Компания" }}
                    translationKey="footer.company.title"
                />
            </Typography>
            <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                {companyLinks.map((item) => (
                    <li key={item.href}>
                        <LocalizedLink
                            className="inline-flex min-h-10 items-center transition-colors duration-300 hover:text-secondary"
                            href={item.href}
                            prefetch={false}
                        >
                            <TranslatedText
                                fallback={item.fallback}
                                translationKey={item.key}
                            />
                        </LocalizedLink>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
