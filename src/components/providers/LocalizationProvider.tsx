"use client";

import {
    createContext,
    useContext,
    type ReactNode,
} from "react";

import type { Locale } from "@/lib/locales";
import {
    createTranslator,
    type TranslationFallback,
    type TranslationMap,
} from "@/lib/translations";

type LocalizationContextValue = {
    locale: Locale;
    translations: TranslationMap;
    t: (key: string, fallback: TranslationFallback) => string;
};

const LocalizationContext = createContext<LocalizationContextValue | null>(null);

export default function LocalizationProvider({
    children,
    locale,
    translations,
}: {
    children: ReactNode;
    locale: Locale;
    translations: TranslationMap;
}) {
    const t = createTranslator(translations, locale);

    return (
        <LocalizationContext.Provider value={{ locale, translations, t }}>
            {children}
        </LocalizationContext.Provider>
    );
}

export function useLocalization() {
    const context = useContext(LocalizationContext);

    if (!context) {
        throw new Error(
            "useLocalization must be used within LocalizationProvider.",
        );
    }

    return context;
}
