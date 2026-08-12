const DEFAULT_PUBLIC_API_BASE = "http://localhost:8000/api";

function normalizeBaseUrl(value?: string | null) {
    const fallback = DEFAULT_PUBLIC_API_BASE;
    const raw = value?.trim() || fallback;

    try {
        const url = new URL(raw, typeof window === "undefined" ? undefined : window.location.origin);
        const normalizedPath = url.pathname.replace(/\/+$/, "");

        if (!normalizedPath || normalizedPath === "/") {
            url.pathname = "/api";
        } else {
            url.pathname = normalizedPath;
        }

        return url.toString().replace(/\/$/, "");
    } catch {
        return fallback;
    }
}

export function buildPublicApiUrl(path: string) {
    const normalizedPath = path.startsWith("/") ? path : `/${path}`;

    return `${normalizeBaseUrl(process.env.NEXT_PUBLIC_API_URL)}${normalizedPath}`;
}
