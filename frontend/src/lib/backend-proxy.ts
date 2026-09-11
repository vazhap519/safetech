import "server-only";

import { buildServerApiUrl } from "@/lib/backend-api";

export async function forwardBackendRequest(
    request: Request,
    path: string,
) {
    const body = await request.text();
    const idempotencyKey = request.headers.get("idempotency-key");
    const requestId = request.headers.get("x-request-id") || crypto.randomUUID();

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
                ...(idempotencyKey
                    ? { "Idempotency-Key": idempotencyKey }
                    : {}),
                "X-Request-ID": requestId,
                ...(request.headers.get("x-forwarded-for")
                    ? {
                          "X-Forwarded-For":
                              request.headers.get("x-forwarded-for") || "",
                      }
                    : {}),
            },
            body: body || undefined,
            cache: "no-store",
            // Keep this above the browser timeout. The browser can retry with
            // the same durable idempotency key while this request completes.
            signal: AbortSignal.timeout(20000),
        });

        return new Response(await response.text(), {
            status: response.status,
            headers: {
                "Content-Type":
                    response.headers.get("content-type") ||
                    "application/json; charset=utf-8",
                "X-Request-ID": response.headers.get("x-request-id") || requestId,
            },
        });
    } catch {
        return Response.json(
            { error: "backend_unavailable" },
            { status: 502, headers: { "X-Request-ID": requestId } },
        );
    }
}
