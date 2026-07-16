import { notFound } from "next/navigation";

import { isNonDefaultLocale, supportedLocales } from "@/lib/locales";

export function generateStaticParams() {
    return supportedLocales
        .filter((locale) => isNonDefaultLocale(locale))
        .map((locale) => ({ locale }));
}

export default async function LocaleLayout({
    children,
    params,
}: Readonly<{
    children: React.ReactNode;
    params: Promise<{ locale: string }>;
}>) {
    const { locale } = await params;

    if (!isNonDefaultLocale(locale)) {
        notFound();
    }

    return children;
}
