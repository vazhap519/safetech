import { NextResponse } from "next/server";

import { LOCALE_COOKIE_NAME, normalizeLocale } from "@/lib/locales";

export async function POST(request: Request) {
    const payload = (await request.json().catch(() => ({}))) as {
        locale?: string;
    };
    const locale = normalizeLocale(payload.locale);
    const response = NextResponse.json({ ok: true, locale });

    response.cookies.set(LOCALE_COOKIE_NAME, locale, {
        httpOnly: false,
        maxAge: 60 * 60 * 24 * 365,
        path: "/",
        sameSite: "lax",
    });

    return response;
}
