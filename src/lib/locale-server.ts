import "server-only";

import { cookies } from "next/headers";
import { headers } from "next/headers";

import {
    LOCALE_COOKIE_NAME,
    normalizeLocale,
    type Locale,
} from "@/lib/locales";

export async function getCurrentLocale(): Promise<Locale> {
    const headerStore = await headers();
    const cookieStore = await cookies();

    return normalizeLocale(
        headerStore.get("x-safetech-locale") ||
            cookieStore.get(LOCALE_COOKIE_NAME)?.value,
    );
}
