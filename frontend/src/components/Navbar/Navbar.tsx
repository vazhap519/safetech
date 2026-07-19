import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import LocaleSwitcher from "@/components/LocaleSwitcher";
import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const navigation = [
    { href: "/", key: "nav.home", fallback: { ka: "მთავარი", en: "Home", ru: "Главная" } },
    { href: "/services", key: "nav.services", fallback: { ka: "სერვისები", en: "Services", ru: "Услуги" } },
    { href: "/service-calculator", key: "nav.calculator", fallback: { ka: "კალკულატორი", en: "Calculator", ru: "Калькулятор" } },
    { href: "/projects", key: "nav.projects", fallback: { ka: "პროექტები", en: "Projects", ru: "Проекты" } },
    { href: "/about", key: "nav.about", fallback: { ka: "ჩვენ შესახებ", en: "About", ru: "О нас" } },
    { href: "/contact", key: "nav.contact", fallback: { ka: "კონტაქტი", en: "Contact", ru: "Контакты" } },
];

export default async function Navbar() {
    const { branding, locale, translations } = await getSiteSettings();
    const siteName = branding.siteName;
    const homeLabel = translateText(translations, "nav.home", locale, null);
    const navigationItems = navigation
        .map((item) => ({
            ...item,
            label: translateText(translations, item.key, locale, item.fallback),
        }))
        .filter((item) => item.label);
    const consultationLabel = translateText(
        translations,
        "nav.consultation",
        locale,
        null,
    );
    const hasBrand = Boolean(branding.logo || siteName);

    return (
        <nav
            aria-label={translateText(translations, "nav.region", locale, null)}
            className="fixed top-0 z-50 w-full border-b border-outline-variant/20 bg-surface/90 shadow-2xl shadow-primary/5 backdrop-blur-xl"
        >
            <div className="mx-auto flex h-20 max-w-container-max items-center justify-between px-5 md:px-margin-desktop">
                {hasBrand ? (
                    <LocalizedLink
                        aria-label={homeLabel || siteName}
                        className="flex min-h-11 items-center gap-3 text-primary"
                        href="/"
                    >
                        {branding.logo ? (
                            <Image
                                alt={siteName || homeLabel}
                                className="h-11 w-auto object-contain"
                                height={44}
                                src={branding.logo}
                                unoptimized
                                width={160}
                            />
                        ) : null}
                        {siteName ? (
                            <span className="font-headline-md text-headline-md font-bold tracking-tight">
                                {siteName}
                            </span>
                        ) : null}
                    </LocalizedLink>
                ) : (
                    <span aria-hidden="true" />
                )}

                <ul className="hidden items-center gap-unit-lg lg:flex">
                    {navigationItems.map((item) => (
                        <li key={item.href}>
                            <LocalizedLink
                                className="inline-flex min-h-11 items-center font-label-md text-label-md text-on-surface-variant transition-colors hover:text-primary"
                                href={item.href}
                            >
                                {item.label}
                            </LocalizedLink>
                        </li>
                    ))}
                </ul>

                <div className="hidden items-center gap-3 lg:flex">
                    <LocaleSwitcher currentLocale={locale} variant="header" />
                    {consultationLabel ? (
                        <ConsultationTrigger className="min-h-11 rounded-xl bg-primary-container px-6 py-3 font-medium text-on-primary-container shadow-lg shadow-blue-500/20 transition-all hover:brightness-110">
                            {consultationLabel}
                        </ConsultationTrigger>
                    ) : null}
                </div>

                <details className="group relative lg:hidden">
                    <summary
                        aria-label={translateText(
                            translations,
                            "nav.mobile.open",
                            locale,
                            null,
                        )}
                        className="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl border border-outline-variant/30 bg-surface-container/50 text-on-surface backdrop-blur-xl marker:hidden hover:bg-surface-container-high"
                    >
                        <svg
                            aria-hidden="true"
                            className="h-6 w-6 group-open:hidden"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="1.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5"
                                strokeLinecap="round"
                            />
                        </svg>
                        <svg
                            aria-hidden="true"
                            className="hidden h-6 w-6 group-open:block"
                            fill="none"
                            stroke="currentColor"
                            strokeWidth="1.5"
                            viewBox="0 0 24 24"
                        >
                            <path
                                d="M6 18 18 6M6 6l12 12"
                                strokeLinecap="round"
                            />
                        </svg>
                    </summary>
                    <div className="absolute right-0 top-14 hidden w-[min(20rem,calc(100vw-2.5rem))] rounded-2xl border border-outline-variant/20 bg-surface/95 p-5 shadow-2xl backdrop-blur-2xl group-open:block">
                        <ul className="flex flex-col gap-4">
                            {navigationItems.map((item) => (
                                <li key={item.href}>
                                    <LocalizedLink
                                        className="flex min-h-11 items-center text-on-surface-variant transition-colors hover:text-primary"
                                        href={item.href}
                                    >
                                        {item.label}
                                    </LocalizedLink>
                                </li>
                            ))}
                            <li className="pt-2">
                                <LocaleSwitcher
                                    currentLocale={locale}
                                    variant="header"
                                />
                            </li>
                            {consultationLabel ? (
                                <li>
                                    <ConsultationTrigger className="min-h-11 w-full rounded-xl bg-primary-container px-6 py-3 text-center font-medium text-on-primary-container">
                                        {consultationLabel}
                                    </ConsultationTrigger>
                                </li>
                            ) : null}
                        </ul>
                    </div>
                </details>
            </div>
        </nav>
    );
}
