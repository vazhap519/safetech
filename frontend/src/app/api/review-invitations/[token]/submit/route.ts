import { forwardBackendRequest } from "@/lib/backend-proxy";
import { DEFAULT_LOCALE, isSupportedLocale } from "@/lib/locales";

function localeFromReferer(request: Request) {
    const referer = request.headers.get("referer");

    if (!referer) return DEFAULT_LOCALE;

    try {
        const firstSegment = new URL(referer).pathname.split("/").filter(Boolean)[0];

        return isSupportedLocale(firstSegment) ? firstSegment : DEFAULT_LOCALE;
    } catch {
        return DEFAULT_LOCALE;
    }
}

export async function POST(
    request: Request,
    { params }: { params: Promise<{ token: string }> },
) {
    const { token } = await params;
    const payload = (await request.json().catch(() => ({}))) as Record<
        string,
        unknown
    >;
    const localizedRequest = new Request(request.url, {
        method: "POST",
        headers: request.headers,
        body: JSON.stringify({
            ...payload,
            locale: localeFromReferer(request),
        }),
    });

    return forwardBackendRequest(
        localizedRequest,
        `/review-invitations/${encodeURIComponent(token)}/submit`,
    );
}
