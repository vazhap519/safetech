import LocalizedLink from "@/components/ui/LocalizedLink";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const companyLinks = [
    { href: "/about", key: "footer.company.about", fallback: { ka: "ჩვენ შესახებ", en: "About", ru: "О нас" } },
    { href: "/projects", key: "footer.company.projects", fallback: { ka: "პროექტები", en: "Projects", ru: "Проекты" } },
    { href: "/services", key: "footer.company.services", fallback: { ka: "სერვისები", en: "Services", ru: "Услуги" } },
    { href: "/service-calculator", key: "footer.company.calculator", fallback: { ka: "კალკულატორი", en: "Calculator", ru: "Калькулятор" } },
    { href: "/contact", key: "footer.company.contact", fallback: { ka: "კონტაქტი", en: "Contact", ru: "Контакты" } },
];

export default async function FooterCompany() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "footer.company.title", locale, null);
    const links = companyLinks
        .map((item) => ({
            ...item,
            label: translateText(translations, item.key, locale, item.fallback),
        }))
        .filter((item) => item.label);

    if (!title && !links.length) return null;

    return (
        <nav aria-label={title || undefined} className="space-y-4">
            {title ? (
                <Typography as="h2" variant="footer-title">
                    {title}
                </Typography>
            ) : null}
            {links.length ? (
                <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                    {links.map((item) => (
                        <li key={item.href}>
                            <LocalizedLink
                                className="inline-flex min-h-10 items-center transition-colors duration-300 hover:text-secondary"
                                href={item.href}
                            >
                                {item.label}
                            </LocalizedLink>
                        </li>
                    ))}
                </ul>
            ) : null}
        </nav>
    );
}
