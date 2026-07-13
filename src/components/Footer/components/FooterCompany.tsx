import LocalizedLink from "@/components/ui/LocalizedLink";

import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const companyLinks = [
    {
        href: "/about",
        key: "footer.company.about",
        fallback: { ka: "ჩვენს შესახებ", en: "About", ru: "О нас" },
    },
    {
        href: "/projects",
        key: "footer.company.projects",
        fallback: { ka: "პროექტები", en: "Projects", ru: "Проекты" },
    },
    {
        href: "/services",
        key: "footer.company.services",
        fallback: { ka: "სერვისები", en: "Services", ru: "Услуги" },
    },
    {
        href: "/contact",
        key: "footer.company.contact",
        fallback: { ka: "კონტაქტი", en: "Contact", ru: "Контакты" },
    },
];

export default async function FooterCompany() {
    const { locale, translations } = await getSiteSettings();

    return (
        <nav aria-label="კომპანია" className="space-y-4">
            <Typography as="h2" variant="footer-title">
                {translateText(translations, "footer.company.title", locale, {
                    ka: "კომპანია",
                    en: "Company",
                    ru: "Компания",
                })}
            </Typography>
            <ul className="space-y-2 font-body-md text-body-md text-on-surface-variant">
                {companyLinks.map((item) => (
                    <li key={item.href}>
                        <LocalizedLink
                            className="inline-flex min-h-10 items-center transition-colors duration-300 hover:text-secondary"
                            href={item.href}
                        >
                            {translateText(
                                translations,
                                item.key,
                                locale,
                                item.fallback,
                            )}
                        </LocalizedLink>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
