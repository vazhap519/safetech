"use client";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import type { TranslationFallback } from "@/lib/translations";

type TranslatedTextProps = {
    fallback: TranslationFallback;
    translationKey: string;
};

export default function TranslatedText({
    fallback,
    translationKey,
}: TranslatedTextProps) {
    const { t } = useLocalization();

    return t(translationKey, fallback) || null;
}
