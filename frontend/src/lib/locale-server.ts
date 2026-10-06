import "server-only";

import { cache } from "react";
import { cookies, headers } from "next/headers";

import {
    DEFAULT_LOCALE,
    LOCALE_COOKIE_NAME,
    normalizeLocale,
    type Locale,
} from "@/lib/locales";

export const getCurrentLocale = cache(async (): Promise<Locale> => {
    const [headerStore, cookieStore] = await Promise.all([headers(), cookies()]);

    return normalizeLocale(
        headerStore.get("x-safetech-locale") ||
            cookieStore.get(LOCALE_COOKIE_NAME)?.value ||
            DEFAULT_LOCALE,
    );
});
