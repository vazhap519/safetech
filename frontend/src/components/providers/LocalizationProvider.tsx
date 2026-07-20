"use client";

import {
    createContext,
    useCallback,
    useContext,
    useEffect,
    useMemo,
    useState,
    type ReactNode,
} from "react";
import { usePathname } from "next/navigation";

import {
    getLanguageTag,
    getLocaleFromPath,
    type Locale,
} from "@/lib/locales";
import {
    createTranslator,
    type TranslationFallback,
    type TranslationMap,
} from "@/lib/translations";

type LocalizationContextValue = {
    locale: Locale;
    selectLocale: (locale: Locale) => void;
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
    const pathname = usePathname();
    const pathLocale = pathname ? getLocaleFromPath(pathname) : locale;
    const [selection, setSelection] = useState<{
        locale: Locale;
        pathname: string | null;
    } | null>(null);

    if (selection && selection.pathname !== pathname) {
        setSelection(null);
    }

    const activeLocale =
        selection?.pathname === pathname ? selection.locale : pathLocale;
    const selectLocale = useCallback(
        (nextLocale: Locale) => {
            setSelection({ locale: nextLocale, pathname });
        },
        [pathname],
    );
    const value = useMemo(
        () => ({
            locale: activeLocale,
            selectLocale,
            translations,
            t: createTranslator(translations, activeLocale),
        }),
        [activeLocale, selectLocale, translations],
    );

    useEffect(() => {
        document.documentElement.lang = getLanguageTag(activeLocale);
    }, [activeLocale]);

    return (
        <LocalizationContext.Provider value={value}>
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
