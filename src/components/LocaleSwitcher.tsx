"use client";

import { localeLabels, supportedLocales, type Locale } from "@/lib/locales";

type LocaleSwitcherProps = {
    currentLocale: Locale;
    variant?: "header" | "footer";
};

export default function LocaleSwitcher({
    currentLocale,
    variant = "footer",
}: LocaleSwitcherProps) {
    async function handleLocaleChange(locale: Locale) {
        await fetch("/api/locale", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ locale }),
        });

        window.location.reload();
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
                                    ? "rounded-full bg-primary px-3 py-1.5 text-xs font-semibold text-on-primary"
                                    : "rounded-full px-3 py-1.5 text-xs font-medium text-on-surface-variant transition-colors hover:text-on-surface"
                                : isActive
                                  ? "text-on-surface"
                                  : "text-on-surface-variant transition-colors hover:text-on-surface"
                        }
                        key={locale}
                        onClick={() => void handleLocaleChange(locale)}
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
