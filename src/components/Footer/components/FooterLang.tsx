"use client";

import LocaleSwitcher from "@/components/LocaleSwitcher";
import type { Locale } from "@/lib/locales";

export default function FooterLang({
    currentLocale,
}: {
    currentLocale: Locale;
}) {
    return <LocaleSwitcher currentLocale={currentLocale} variant="footer" />;
}
