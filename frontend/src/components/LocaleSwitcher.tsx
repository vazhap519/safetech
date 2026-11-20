"use client";

import Link from "next/link";
import { usePathname, useSearchParams } from "next/navigation";
import { useSyncExternalStore, type MouseEvent } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import {
    localeLabels,
    localizePath,
    supportedLocales,
    getLanguageTag,
    stripLocalePrefix,
} from "@/lib/locales";

function subscribeToMetadata(onChange: () => void) {
    const observer = new MutationObserver(onChange);
    // Next.js can stream page metadata into the body before hoisting it.
    observer.observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ["href", "hreflang"] });
    return () => observer.disconnect();
}

function translatedLocales(pathname: string) {
    return supportedLocales.filter((locale) => {
        const links = document.querySelectorAll<HTMLLinkElement>(`link[rel="alternate"][hreflang="${getLanguageTag(locale)}"]`);
        return Array.from(links).some((link) => new URL(link.href).pathname === localizePath(pathname, locale));
    }).join(",");
}

type LocaleSwitcherProps = {
    variant?: "header" | "footer";
};

export default function LocaleSwitcher({
    variant = "footer",
}: LocaleSwitcherProps) {
    const { locale: activeLocale, selectLocale } = useLocalization();
    const pathname = usePathname();
    const searchParams = useSearchParams();
    const plainPath = stripLocalePrefix(pathname || "/");
    const isLocalPage = /^\/services\/[^/]+\/[^/]+\/?$/.test(plainPath);
    const availableLocales = useSyncExternalStore(
        subscribeToMetadata,
        () => isLocalPage ? translatedLocales(plainPath) : "",
        () => "",
    ).split(",");

    const wrapperClassName =
        variant === "header"
            ? "flex items-center gap-1 rounded-full border border-outline-variant/30 bg-surface-container/60 p-1"
            : "flex gap-unit-md";

    return (
        <div className={wrapperClassName}>
            {supportedLocales.map((locale) => {
                const isActive = locale === activeLocale;
                const query = searchParams.toString();
                // Missing translations lead to the translated service overview,
                // rather than a nonexistent local page. Before metadata arrives,
                // the conservative parent link is also safe for server HTML.
                const currentPath = isLocalPage && !availableLocales.includes(locale)
                    ? plainPath.split("/").slice(0, 3).join("/")
                    : pathname || "/";
                const nextPath = localizePath(
                    query ? `${currentPath}?${query}` : currentPath,
                    locale,
                );
                const className =
                    variant === "header"
                        ? isActive
                            ? "inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-full bg-primary px-3 py-1.5 text-xs font-semibold text-on-primary"
                            : "inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-full px-3 py-1.5 text-xs font-medium text-on-surface-variant transition-colors hover:text-on-surface"
                        : isActive
                          ? "text-on-surface"
                          : "text-on-surface-variant transition-colors hover:text-on-surface";
                const label =
                    variant === "header"
                        ? locale.toUpperCase()
                        : localeLabels[locale];
                const style =
                    variant === "header"
                        ? { minHeight: 44, minWidth: 44 }
                        : undefined;

                function handleLocaleClick(
                    event: MouseEvent<HTMLAnchorElement>,
                ) {
                    const isPlainLeftClick =
                        event.button === 0 &&
                        !event.altKey &&
                        !event.ctrlKey &&
                        !event.metaKey &&
                        !event.shiftKey;

                    if (isPlainLeftClick && !event.defaultPrevented) {
                        selectLocale(locale);
                    }
                }

                return isActive ? (
                    <span
                        aria-current="page"
                        className={className}
                        key={locale}
                        style={style}
                    >
                        {label}
                    </span>
                ) : (
                    <Link
                        className={className}
                        href={nextPath}
                        key={locale}
                        onClick={handleLocaleClick}
                        prefetch
                        replace
                        scroll={false}
                        style={style}
                    >
                        {label}
                    </Link>
                );
            })}
        </div>
    );
}
