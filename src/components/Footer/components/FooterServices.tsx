import Link from "next/link";

import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const serviceLinks = [
    {
        href: "/services/cctv",
        key: "footer.services.cctv",
        fallback: {
            ka: "ვიდეომეთვალყურეობა",
            en: "Video Surveillance",
            ru: "Видеонаблюдение",
        },
    },
    {
        href: "/services/access-control",
        key: "footer.services.access",
        fallback: {
            ka: "დაშვების კონტროლი",
            en: "Access Control",
            ru: "Контроль доступа",
        },
    },
    {
        href: "/services/networking",
        key: "footer.services.networking",
        fallback: {
            ka: "ქსელური ინფრასტრუქტურა",
            en: "Network Infrastructure",
            ru: "Сетевая инфраструктура",
        },
    },
    {
        href: "/services/server-infrastructure",
        key: "footer.services.servers",
        fallback: {
            ka: "სერვერული სისტემები",
            en: "Server Systems",
            ru: "Серверные системы",
        },
    },
];

export default async function FooterServices() {
    const { locale, translations } = await getSiteSettings();

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
                {serviceLinks.map((item) => (
                    <li key={item.href}>
                        <Link
                            className="transition-colors duration-300 hover:text-secondary"
                            href={item.href}
                        >
                            {translateText(
                                translations,
                                item.key,
                                locale,
                                item.fallback,
                            )}
                        </Link>
                    </li>
                ))}
            </ul>
        </nav>
    );
}
