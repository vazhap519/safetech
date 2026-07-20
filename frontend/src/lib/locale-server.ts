import "server-only";

import { cache } from "react";
import { headers } from "next/headers";

import {
    DEFAULT_LOCALE,
    normalizeLocale,
    type Locale,
} from "@/lib/locales";

export const getCurrentLocale = cache(async (): Promise<Locale> => {
    const headerStore = await headers();

    return normalizeLocale(
        headerStore.get("x-safetech-locale") || DEFAULT_LOCALE,
    );
});
