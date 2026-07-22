"use client";

import { useEffect, useRef } from "react";
import { usePathname } from "next/navigation";

import LocaleSwitcher from "@/components/LocaleSwitcher";
import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import { localizePath } from "@/lib/locales";
import { primaryNavigation } from "@/lib/navigation";

type NavbarClientProps = {
    logo: string | null;
    siteName: string;
};

export default function NavbarClient({ logo, siteName }: NavbarClientProps) {
    const { locale, t } = useLocalization();
    const pathname = usePathname() || "/";
    const mobileMenuRef = useRef<HTMLDetailsElement>(null);
    const homeLabel = t("nav.home", primaryNavigation[0].fallback);
    const consultationLabel = t("nav.consultation", {
        ka: "კონსულტაცია",
        en: "Consultation",
        ru: "Консультация",
    });
    const navigationLabel = t("nav.region", {
        ka: "მთავარი ნავიგაცია",
        en: "Main navigation",
        ru: "Основная навигация",
    });
    const mobileMenuLabel = t("nav.mobile.open", {
        ka: "მენიუს გახსნა",
        en: "Open menu",
        ru: "Открыть меню",
    });
    const navigationItems = primaryNavigation
        .map((item) => ({
            ...item,
            label: t(item.key, item.fallback),
        }))
        .filter((item) => item.label);
    const hasBrand = Boolean(logo || siteName);

    useEffect(() => {
        if (mobileMenuRef.current) {
            mobileMenuRef.current.open = false;
        }
    }, [pathname]);

    function isCurrentPage(href: string) {
        const localizedHref = localizePath(href, locale);

        return href === "/"
            ? pathname === localizedHref
            : pathname === localizedHref || pathname.startsWith(`${localizedHref}/`);
    }

    function closeMobileMenu() {
        if (mobileMenuRef.current) {
            mobileMenuRef.current.open = false;
        }
    }

    return (
        <nav
            aria-label={navigationLabel || undefined}
            className="fixed top-0 z-50 w-full border-b border-outline-variant/20 bg-surface/95 shadow-lg shadow-black/20 backdrop-blur-xl"
        >
            <div className="mx-auto flex h-[76px] max-w-container-max items-center justify-between gap-4 px-5 md:px-8 xl:px-10 2xl:px-14">
                {hasBrand ? (
                    <LocalizedLink
                        aria-label={[siteName, homeLabel]
                            .filter(Boolean)
                            .join(" - ")}
                        className="flex min-h-11 shrink-0 items-center gap-3 text-primary"
                        href="/"
                        onClick={closeMobileMenu}
                        prefetch={false}
                    >
                        {logo ? (
                            <Image
                                alt={siteName || homeLabel}
                                className="h-10 w-auto object-contain"
                                height={40}
                                src={logo}
                                unoptimized
                                width={160}
                            />
                        ) : null}
                        {siteName ? (
                            <span className="font-headline-md text-[22px] font-bold leading-none">
                                {siteName}
                            </span>
                        ) : null}
                    </LocalizedLink>
                ) : (
                    <span aria-hidden="true" />
                )}

                <ul className="ml-auto hidden items-center gap-4 xl:flex min-[1440px]:gap-5">
                    {navigationItems.map((item) => {
                        const isCurrent = isCurrentPage(item.href);

                        return (
                            <li
                                className={item.href === "/" ? "hidden 2xl:block" : undefined}
                                key={item.href}
                            >
                                <LocalizedLink
                                    aria-current={isCurrent ? "page" : undefined}
                                    className="inline-flex min-h-11 items-center whitespace-nowrap font-label-md text-[13px] font-semibold text-on-surface-variant transition-colors hover:text-primary aria-[current=page]:text-primary min-[1440px]:text-sm"
                                    href={item.href}
                                    prefetch={false}
                                >
                                    {item.label}
                                </LocalizedLink>
                            </li>
                        );
                    })}
                </ul>

                <div className="hidden shrink-0 items-center gap-2 xl:flex">
                    <LocaleSwitcher variant="header" />
                    {consultationLabel ? (
                        <ConsultationTrigger className="min-h-11 rounded-lg bg-primary-container px-5 py-3 text-sm font-semibold text-on-primary-container shadow-lg shadow-blue-500/20 transition-all hover:brightness-110">
                            {consultationLabel}
                        </ConsultationTrigger>
                    ) : null}
                </div>

                <details className="group relative ml-auto xl:hidden" ref={mobileMenuRef}>
                    <summary
                        aria-label={mobileMenuLabel || undefined}
                        className="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-lg border border-outline-variant/30 bg-surface-container/50 text-on-surface backdrop-blur-xl marker:hidden hover:bg-surface-container-high"
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
                    <div className="absolute right-0 top-14 hidden w-[min(20rem,calc(100vw-2.5rem))] rounded-lg border border-outline-variant/20 bg-surface/98 p-5 shadow-2xl backdrop-blur-2xl group-open:block">
                        <ul className="flex flex-col gap-4">
                            {navigationItems.map((item) => (
                                <li key={item.href}>
                                    <LocalizedLink
                                        aria-current={isCurrentPage(item.href) ? "page" : undefined}
                                        className="flex min-h-11 items-center text-on-surface-variant transition-colors hover:text-primary aria-[current=page]:text-primary"
                                        href={item.href}
                                        onClick={closeMobileMenu}
                                        prefetch={false}
                                    >
                                        {item.label}
                                    </LocalizedLink>
                                </li>
                            ))}
                            <li className="pt-2">
                                <LocaleSwitcher variant="header" />
                            </li>
                            {consultationLabel ? (
                                <li>
                                    <ConsultationTrigger className="min-h-11 w-full rounded-lg bg-primary-container px-6 py-3 text-center font-semibold text-on-primary-container">
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
