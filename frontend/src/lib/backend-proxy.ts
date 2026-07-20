import "server-only";

import { buildServerApiUrl } from "@/lib/backend-api";

export async function forwardBackendRequest(
    request: Request,
    path: string,
) {
    const body = await request.text();

    try {
        const response = await fetch(buildServerApiUrl(path), {
            method: request.method,
            headers: {
                Accept: request.headers.get("accept") || "application/json",
                "Content-Type":
                    request.headers.get("content-type") || "application/json",
                "User-Agent":
                    request.headers.get("user-agent") ||
                    "SafeTech Frontend Proxy",
                ...(request.headers.get("x-forwarded-for")
                    ? {
                          "X-Forwarded-For":
                              request.headers.get("x-forwarded-for") || "",
                      }
                    : {}),
            },
            body: body || undefined,
            cache: "no-store",
            signal: AbortSignal.timeout(10000),
        });

        return new Response(await response.text(), {
            status: response.status,
            headers: {
                "Content-Type":
                    response.headers.get("content-type") ||
                    "application/json; charset=utf-8",
            },
        });
    } catch {
        return Response.json(
            { error: "backend_unavailable" },
            { status: 502 },
        );
    }
}
