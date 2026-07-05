import "server-only";

import { cookies } from "next/headers";

import {
    LOCALE_COOKIE_NAME,
    normalizeLocale,
    type Locale,
} from "@/lib/locales";

export async function getCurrentLocale(): Promise<Locale> {
    const cookieStore = await cookies();

    return normalizeLocale(cookieStore.get(LOCALE_COOKIE_NAME)?.value);
}
