import "server-only";

const DEFAULT_SERVER_API_BASE = "http://127.0.0.1:8000/api";

function normalizeBaseUrl(value?: string | null) {
    if (!value) {
        return null;
    }

    const trimmed = value.trim();

    if (!trimmed) {
        return null;
    }

    try {
        const url = new URL(trimmed);
        const normalizedPath = url.pathname.replace(/\/+$/, "");

        // Laravel's routes/api.php is mounted under /api. In production it is
        // easy to configure only the API origin (https://api.safetech.ge),
        // which would otherwise make every request hit /projects, /services,
        // etc. and return 404. Keep explicit custom paths untouched, but add
        // /api when the configured value is only an origin.
        if (!normalizedPath || normalizedPath === "/") {
            url.pathname = "/api";
        } else {
            url.pathname = normalizedPath;
        }

        return url.toString().replace(/\/$/, "");
    } catch {
        return null;
    }
}

function getConfiguredPublicApiBase() {
    return normalizeBaseUrl(process.env.NEXT_PUBLIC_API_URL);
}

export function getServerApiBase() {
    return (
        normalizeBaseUrl(process.env.BACKEND_API_URL) ||
        getConfiguredPublicApiBase() ||
        DEFAULT_SERVER_API_BASE
    );
}

export function getPublicApiOrigin() {
    const configuredPublicBase = getConfiguredPublicApiBase();

    if (configuredPublicBase) {
        return new URL(configuredPublicBase).origin;
    }

    try {
        return new URL(getServerApiBase()).origin;
    } catch {
        return "";
    }
}

export function buildServerApiUrl(path: string) {
    const normalizedPath = path.startsWith("/") ? path : `/${path}`;

    return `${getServerApiBase()}${normalizedPath}`;
}
