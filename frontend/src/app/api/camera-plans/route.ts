import { buildServerApiUrl } from "@/lib/backend-api";

export const runtime = "nodejs";

export async function POST(request: Request) {
    const contentType = request.headers.get("content-type") || "";
    if (!contentType.startsWith("multipart/form-data;")) {
        return Response.json({ message: "multipart/form-data required" }, { status: 415 });
    }
    const data = await request.arrayBuffer();
    if (data.byteLength > 6 * 1024 * 1024) {
        return Response.json({ message: "Request too large" }, { status: 413 });
    }
    try {
        const upstream = await fetch(buildServerApiUrl("/camera-plans"), {
            method: "POST",
            headers: {
                "Content-Type": contentType,
                Accept: "application/json",
                "User-Agent": request.headers.get("user-agent") || "SafeTech Planner",
                "X-Request-ID": request.headers.get("x-request-id") || crypto.randomUUID(),
            },
            body: new Blob([data]),
            cache: "no-store",
            signal: AbortSignal.timeout(25000),
        });
        return new Response(await upstream.text(), {
            status: upstream.status,
            headers: { "Content-Type": "application/json; charset=utf-8", "Cache-Control": "no-store" },
        });
    } catch {
        return Response.json({ message: "Backend temporarily unavailable" }, { status: 502 });
    }
}
