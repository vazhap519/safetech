import "server-only";

import { buildServerApiUrl } from "@/lib/backend-api";

type ProbeParams = Record<string, string | number | boolean | null | undefined>;

export class BackendResourceUnavailableError extends Error {
    constructor(path: string, status?: number, cause?: unknown) {
        super(
            status
                ? `Backend resource ${path} is temporarily unavailable (HTTP ${status}).`
                : `Backend resource ${path} is temporarily unavailable.`,
            cause === undefined ? undefined : { cause },
        );
        this.name = "BackendResourceUnavailableError";
    }
}

function withQuery(path: string, params: ProbeParams): string {
    const query = new URLSearchParams();

    for (const [key, value] of Object.entries(params)) {
        if (value === null || value === undefined || value === "") continue;
        query.set(key, String(value));
    }

    const suffix = query.toString();
    return suffix ? `${path}?${suffix}` : path;
}

/**
 * Call this only after a normal CMS detail fetch returned no resource.
 * A real upstream 404 is allowed to remain a Next.js 404. Network errors,
 * timeouts, 429s, 5xx responses, and inconsistent 2xx responses are treated
 * as temporary backend failures so search engines are not told that a valid
 * CMS page has permanently disappeared.
 */
export async function confirmBackendResourceNotFound(
    path: string,
    params: ProbeParams = {},
): Promise<void> {
    const resourcePath = withQuery(path, params);
    const url = buildServerApiUrl(resourcePath);
    let lastError: unknown;

    for (let attempt = 0; attempt < 2; attempt += 1) {
        try {
            const response = await fetch(url, {
                cache: "no-store",
                signal: AbortSignal.timeout(7000),
            });

            if (response.status === 404) return;

            if (response.ok) {
                throw new BackendResourceUnavailableError(resourcePath, response.status);
            }

            if ((response.status === 429 || response.status >= 500) && attempt === 0) {
                continue;
            }

            throw new BackendResourceUnavailableError(resourcePath, response.status);
        } catch (error) {
            if (error instanceof BackendResourceUnavailableError) throw error;

            lastError = error;

            if (attempt === 0) continue;
        }
    }

    throw new BackendResourceUnavailableError(resourcePath, undefined, lastError);
}
