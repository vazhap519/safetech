import { buildServerApiUrl } from "@/lib/backend-api";

export const runtime = "nodejs";

// Same-origin proxy: OpenAI credential stays exclusively in Laravel.
// Do not cache or log private house drawings or customer inputs.
export async function POST(request: Request) {
    const contentType = request.headers.get("content-type") || "";
    if (!contentType.startsWith("multipart/form-data;")) {
        return Response.json({ message: "multipart/form-data required" }, { status: 415 });
    }

    const length = Number(request.headers.get("content-length") || 0);
    if (length > 6 * 1024 * 1024) {
        return Response.json({ message: "Request too large" }, { status: 413 });
    }

    try {
        const data = await request.arrayBuffer();
        if (data.byteLength > 6 * 1024 * 1024) {
            return Response.json({ message: "Request too large" }, { status: 413 });
        }

        const upstream = await fetch(buildServerApiUrl("/camera-plans/vision"), {
            method: "POST",
            headers: {
                "Content-Type": contentType,
                Accept: "application/json",
                "X-Request-ID": request.headers.get("x-request-id") || crypto.randomUUID(),
            },
            body: new Blob([data]),
            cache: "no-store",
            signal: AbortSignal.timeout(80000),
        });

        return new Response(await upstream.text(), {
            status: upstream.status,
            headers: {
                "Content-Type": "application/json; charset=utf-8",
                "Cache-Control": "no-store, private",
            },
        });
    } catch {
        return Response.json({ message: "AI planning is temporarily unavailable" }, {
            status: 503,
            headers: { "Cache-Control": "no-store" },
        });
    }
}
