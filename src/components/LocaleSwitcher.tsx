"use client";

import { usePathname, useRouter, useSearchParams } from "next/navigation";

import {
    localeLabels,
    localizePath,
    supportedLocales,
    type Locale,
} from "@/lib/locales";

type LocaleSwitcherProps = {
    currentLocale: Locale;
    variant?: "header" | "footer";
};

export default function LocaleSwitcher({
    currentLocale,
    variant = "footer",
}: LocaleSwitcherProps) {
    const pathname = usePathname();
    const router = useRouter();
    const searchParams = useSearchParams();

    async function handleLocaleChange(locale: Locale) {
        if (locale === currentLocale) {
            return;
        }

        try {
            await fetch("/api/locale", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ locale }),
            });
        } catch {
            // Navigation still updates the visible locale path if persistence fails.
        }

        const query = searchParams.toString();
        const currentPath = pathname || "/";
        const nextPath = localizePath(
            query ? `${currentPath}?${query}` : currentPath,
            locale,
        );

        router.push(nextPath);
        router.refresh();
    }

    const wrapperClassName =
        variant === "header"
            ? "flex items-center gap-1 rounded-full border border-outline-variant/30 bg-surface-container/60 p-1"
            : "flex gap-unit-md";

    return (
        <div className={wrapperClassName}>
            {supportedLocales.map((locale) => {
                const isActive = locale === currentLocale;

                return (
                    <button
                        aria-pressed={isActive}
                        className={
                            variant === "header"
                                ? isActive
                                    ? "inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-full bg-primary px-3 py-1.5 text-xs font-semibold text-on-primary"
                                    : "inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-full px-3 py-1.5 text-xs font-medium text-on-surface-variant transition-colors hover:text-on-surface"
                                : isActive
                                  ? "text-on-surface"
                                  : "text-on-surface-variant transition-colors hover:text-on-surface"
                        }
                        key={locale}
                        onClick={() => void handleLocaleChange(locale)}
                        style={
                            variant === "header"
                                ? { minHeight: 44, minWidth: 44 }
                                : undefined
                        }
                        type="button"
                    >
                        {variant === "header"
                            ? locale.toUpperCase()
                            : localeLabels[locale]}
                    </button>
                );
            })}
        </div>
    );
}
