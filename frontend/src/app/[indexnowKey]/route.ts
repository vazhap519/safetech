import { NextResponse, type NextRequest } from "next/server";

import { getSiteSettings } from "@/lib/site-settings";

const INDEXNOW_KEY_PATTERN = /^[a-f0-9]{32,128}$/i;

export const dynamic = "force-dynamic";

export async function GET(
    _request: NextRequest,
    context: { params: Promise<{ indexnowKey: string }> },
) {
    const { indexnowKey } = await context.params;
    const requestedKey = indexnowKey.trim();

    if (!INDEXNOW_KEY_PATTERN.test(requestedKey)) {
        return new NextResponse(null, { status: 404 });
    }

    const { integrations } = await getSiteSettings();
    const configuredKey = integrations.indexNowKey.trim();

    if (
        !configuredKey ||
        !INDEXNOW_KEY_PATTERN.test(configuredKey) ||
        requestedKey.toLowerCase() !== configuredKey.toLowerCase()
    ) {
        return new NextResponse(null, { status: 404 });
    }

    return new NextResponse(configuredKey, {
        status: 200,
        headers: {
            "Content-Type": "text/plain; charset=utf-8",
            "Cache-Control": "no-store",
            "X-Robots-Tag": "noindex, nofollow, nosnippet",
        },
    });
}
