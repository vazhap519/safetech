import clsx from "clsx";
import type { ReactNode } from "react";

type IconProps = {
    name: string;
    className?: string;
    title?: string;
};

export const CONTENT_ICON_NAMES = [
    "settings",
    "security",
    "shield_lock",
    "verified",
    "fingerprint",
    "videocam",
    "lan",
    "dns",
    "wifi",
    "storage",
    "backup",
    "cloud_done",
    "monitoring",
    "query_stats",
    "speed",
    "business",
    "apartment",
    "storefront",
    "warehouse",
    "manufacturing",
    "directions_car",
    "door_front",
    "toll",
    "support_agent",
    "location_on",
    "call",
    "mail",
    "history",
    "architecture",
    "schema",
    "hub",
    "dynamic_feed",
    "settings_input_component",
] as const;

export type ContentIconName = (typeof CONTENT_ICON_NAMES)[number];

const strokeProps = {
    fill: "none",
    stroke: "currentColor",
    strokeLinecap: "round" as const,
    strokeLinejoin: "round" as const,
    strokeWidth: 1.8,
};

const iconNodes = {
    "apartment": (
        <>
            <path d="M5 21V4.5A1.5 1.5 0 0 1 6.5 3h11A1.5 1.5 0 0 1 19 4.5V21" />
            <path d="M3 21h18M8 7h2M14 7h2M8 11h2M14 11h2M8 15h2M14 15h2M10 21v-3h4v3" />
        </>
    ),
    "arrow-right": (
        <>
            <path d="M5 12h14" />
            <path d="m13 5 7 7-7 7" />
        </>
    ),
    "badge-check": (
        <>
            <path d="M12 3.5 15 5l3.4-.1.1 3.4L20.5 12l-2 3.7-.1 3.4-3.4-.1-3 1.5L8.9 19l-3.4.1-.1-3.4L3.5 12l2-3.7.1-3.4L9 5l3-1.5Z" />
            <path d="m8.5 12 2.3 2.3 4.7-4.8" />
        </>
    ),
    "battery": (
        <>
            <rect x="3" y="7" width="17" height="10" rx="2" />
            <path d="M20 10h1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-1M10 9l-2 4h2l-1 3 5-6h-2l1-2" />
        </>
    ),
    "brackets-square": (
        <>
            <path d="M9 5H6a1 1 0 0 0-1 1v3M15 5h3a1 1 0 0 1 1 1v3M9 19H6a1 1 0 0 1-1-1v-3M15 19h3a1 1 0 0 0 1-1v-3" />
            <path d="m10 9-2 3 2 3M14 9l2 3-2 3" />
        </>
    ),
    "briefcase": (
        <>
            <rect x="3" y="7" width="18" height="12" rx="2" />
            <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 11h18M10 11v2h4v-2" />
        </>
    ),
    "building": (
        <>
            <path d="M4 21V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v16M16 21v-9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v9" />
            <path d="M8 8h4M8 12h4M8 16h4M3 21h18" />
        </>
    ),
    "camera": (
        <>
            <rect x="3" y="7" width="14" height="10" rx="2" />
            <path d="m17 10 4-2v8l-4-2" />
            <circle cx="10" cy="12" r="2.5" />
        </>
    ),
    "car": (
        <>
            <path d="m5 16 1-5a2 2 0 0 1 2-1.5h8a2 2 0 0 1 2 1.5l1 5" />
            <path d="M4 16h16v2a1 1 0 0 1-1 1h-1v-1a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v1H5a1 1 0 0 1-1-1v-2Z" />
            <circle cx="7.5" cy="15.5" r="1" />
            <circle cx="16.5" cy="15.5" r="1" />
        </>
    ),
    "chart": (
        <>
            <path d="M4 20h16M6 17V9M12 17V5M18 17v-6" />
        </>
    ),
    "chart-up": (
        <>
            <path d="M4 19h16M6 15l4-4 3 3 5-6M18 8h-3V5" />
        </>
    ),
    "check-circle": (
        <>
            <circle cx="12" cy="12" r="8.5" />
            <path d="m8.5 12 2.4 2.4 4.8-4.8" />
        </>
    ),
    "chevron-down": <path d="m6 9 6 6 6-6" />,
    "chevron-left": <path d="m15 6-6 6 6 6" />,
    "chevron-right": <path d="m9 6 6 6-6 6" />,
    "circle-help": (
        <>
            <circle cx="12" cy="12" r="8.5" />
            <path d="M9.7 9a2.6 2.6 0 1 1 4.3 2c-1.2.9-2 1.3-2 3M12 17h.01" />
        </>
    ),
    "clock": (
        <>
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7.5v5l3 2" />
        </>
    ),
    "cloud-arrow-up": (
        <>
            <path d="M7 18a4 4 0 0 1-.6-8A5.5 5.5 0 0 1 17 8.6 4 4 0 1 1 1 7.4H7Z" />
            <path d="M12 15.5V10m-2.5 2.5L12 10l2.5 2.5" />
        </>
    ),
    "cloud-check": (
        <>
            <path d="M7 18a4 4 0 0 1-.6-8A5.5 5.5 0 0 1 17 8.6 4 4 0 1 1 1 7.4H7Z" />
            <path d="m9.5 13 1.8 1.8 3.4-3.6" />
        </>
    ),
    "cpu": (
        <>
            <rect x="7" y="7" width="10" height="10" rx="2" />
            <path d="M9.5 1.5v3M14.5 1.5v3M9.5 19.5v3M14.5 19.5v3M1.5 9.5h3M1.5 14.5h3M19.5 9.5h3M19.5 14.5h3M10 10h4v4h-4z" />
        </>
    ),
    "door": (
        <>
            <path d="M6 21V5.5A1.5 1.5 0 0 1 7.5 4h7A1.5 1.5 0 0 1 16 5.5V21M6 21h12" />
            <circle cx="13" cy="12" r=".8" />
        </>
    ),
    "draft": (
        <>
            <path d="M4 18V6a2 2 0 0 1 2-2h12v12a2 2 0 0 1-2 2H4Z" />
            <path d="M8 8h6M8 12h8M8 16h5M18 4l2 2" />
        </>
    ),
    "eye": (
        <>
            <path d="M2.5 12s3.6-6 9.5-6 9.5 6 9.5 6-3.6 6-9.5 6-9.5-6-9.5-6Z" />
            <circle cx="12" cy="12" r="2.5" />
        </>
    ),
    "factory": (
        <>
            <path d="M3 21V9l6 3V9l6 3V5l6-2v18H3Z" />
            <path d="M7 21v-4h4v4M16 9h.01M16 13h.01M16 17h.01" />
        </>
    ),
    "feed": (
        <>
            <rect x="4" y="4" width="16" height="4" rx="1" />
            <rect x="4" y="10" width="16" height="4" rx="1" />
            <rect x="4" y="16" width="16" height="4" rx="1" />
            <path d="M7 6h5M7 12h9M7 18h7" />
        </>
    ),
    "fingerprint": (
        <>
            <path d="M9 8a3 3 0 1 1 6 0v2.5M7.5 10.5V9A4.5 4.5 0 1 1 16.5 9v3M6 12v-1a6 6 0 1 1 12 0v1.5M9 14v1a3 3 0 0 0 6 0v-3M12 18v2.5" />
        </>
    ),
    "gauge": (
        <>
            <path d="M4.5 17a8 8 0 1 1 15 0M12 12l4-4" />
            <circle cx="12" cy="12" r="1" />
        </>
    ),
    "gate": (
        <>
            <path d="M4 20V8h4v12M16 20V8h4v12M8 12h8M8 16h8M12 4v16" />
        </>
    ),
    "gear": (
        <>
            <circle cx="12" cy="12" r="3.5" />
            <path d="m12 2 1 2.3 2.6.5.6 2.6 2.2 1.3-.8 2.5.8 2.5-2.2 1.3-.6 2.6-2.6.5L12 22l-1-2.3-2.6-.5-.6-2.6-2.2-1.3.8-2.5-.8-2.5 2.2-1.3.6-2.6 2.6-.5L12 2Z" />
        </>
    ),
    "hard-drive": (
        <>
            <rect x="4" y="5" width="16" height="14" rx="2" />
            <path d="M7 15h10M8.5 10h7" />
            <circle cx="16.5" cy="15.5" r=".8" />
        </>
    ),
    "headset": (
        <>
            <path d="M4 13a8 8 0 1 1 16 0" />
            <rect x="3" y="12" width="3.5" height="6" rx="1.5" />
            <rect x="17.5" y="12" width="3.5" height="6" rx="1.5" />
            <path d="M18 18a3 3 0 0 1-3 3h-1" />
        </>
    ),
    "history": (
        <>
            <path d="M4.5 7.5H8V4M7.8 7.8A7.5 7.5 0 1 1 4.5 12M12 8v4l2.5 1.5" />
        </>
    ),
    "hub": (
        <>
            <circle cx="12" cy="12" r="3" />
            <circle cx="12" cy="3.5" r="1.7" />
            <circle cx="20" cy="12" r="1.7" />
            <circle cx="12" cy="20.5" r="1.7" />
            <circle cx="4" cy="12" r="1.7" />
            <path d="M12 8.8V5.2M15.2 12h3.1M12 15.2v3.6M8.8 12H5.7" />
        </>
    ),
    "id-badge": (
        <>
            <rect x="4" y="4.5" width="16" height="15" rx="2.5" />
            <circle cx="9" cy="10" r="2" />
            <path d="M6.8 15a3.6 3.6 0 0 1 4.4 0M14.5 9.5H18M14.5 13H18M14.5 16.5H17" />
        </>
    ),
    "key": (
        <>
            <circle cx="8.5" cy="12" r="3.5" />
            <path d="M12 12h8M16 12v2M18 12v2" />
        </>
    ),
    "mail": (
        <>
            <rect x="3" y="5" width="18" height="14" rx="2" />
            <path d="m4 7 8 6 8-6" />
        </>
    ),
    "map-pin": (
        <>
            <path d="M12 21s6-5.7 6-11a6 6 0 1 0-12 0c0 5.3 6 11 6 11Z" />
            <circle cx="12" cy="10" r="2" />
        </>
    ),
    "monitor-pulse": (
        <>
            <rect x="3" y="5" width="18" height="12" rx="2" />
            <path d="M8 21h8M12 17v4M6 11h2.5L10 8l2.5 8 1.5-4h3" />
        </>
    ),
    "network": (
        <>
            <rect x="9" y="3" width="6" height="4" rx="1" />
            <rect x="3" y="16" width="6" height="4" rx="1" />
            <rect x="15" y="16" width="6" height="4" rx="1" />
            <path d="M12 7v4M12 11H6v5M12 11h6v5" />
        </>
    ),
    "phone": <path d="M6.5 4.5h3L11 8l-1.5 2.2a13.5 13.5 0 0 0 4.3 4.3L16 13l3.5 1.5v3a2 2 0 0 1-2 2A15 15 0 0 1 4.5 6.5a2 2 0 0 1 2-2Z" />,
    "scan": (
        <>
            <path d="M8 4H5a1 1 0 0 0-1 1v3M16 4h3a1 1 0 0 1 1 1v3M8 20H5a1 1 0 0 1-1-1v-3M16 20h3a1 1 0 0 0 1-1v-3M7 12h10" />
        </>
    ),
    "server": (
        <>
            <rect x="4" y="4" width="16" height="6" rx="1.5" />
            <rect x="4" y="14" width="16" height="6" rx="1.5" />
            <path d="M8 7h.01M12 7h.01M8 17h.01M12 17h.01" />
        </>
    ),
    "share-nodes": (
        <>
            <circle cx="6" cy="12" r="2" />
            <circle cx="18" cy="7" r="2" />
            <circle cx="18" cy="17" r="2" />
            <path d="m7.7 11 8.6-3M7.7 13l8.6 3" />
        </>
    ),
    "shield": <path d="M12 3.5 18 6v5.6c0 4-2.5 7.7-6 8.9-3.5-1.2-6-4.9-6-8.9V6l6-2.5Z" />,
    "shield-lock": (
        <>
            <path d="M12 3.5 18 6v5.6c0 4-2.5 7.7-6 8.9-3.5-1.2-6-4.9-6-8.9V6l6-2.5Z" />
            <rect x="9.2" y="11" width="5.6" height="4.4" rx="1" />
            <path d="M10.5 11v-1a1.5 1.5 0 1 1 3 0v1" />
        </>
    ),
    "sliders": (
        <>
            <path d="M5 7h14M5 12h14M5 17h14" />
            <circle cx="9" cy="7" r="1.2" />
            <circle cx="15" cy="12" r="1.2" />
            <circle cx="11" cy="17" r="1.2" />
        </>
    ),
    "storefront": (
        <>
            <path d="M4 8h16l-1 4a2 2 0 0 1-2 1.5H7A2 2 0 0 1 5 12L4 8Z" />
            <path d="M6 8V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2M6 13.5V20h12v-6.5M10 20v-4h4v4" />
        </>
    ),
    "triangle-alert": (
        <>
            <path d="M12 4 20 19H4L12 4ZM12 9v4M12 16h.01" />
        </>
    ),
    "warehouse": (
        <>
            <path d="M3 10 12 4l9 6v10H3V10Z" />
            <path d="M8 20v-6h8v6M7 10h10" />
        </>
    ),
    "wifi": (
        <>
            <path d="M5 9a10 10 0 0 1 14 0M8 12a6 6 0 0 1 8 0M10.5 14.5a2.5 2.5 0 0 1 3 0" />
            <circle cx="12" cy="18" r=".8" />
        </>
    ),
    "workflow": (
        <>
            <rect x="3" y="4" width="6" height="4" rx="1" />
            <rect x="15" y="4" width="6" height="4" rx="1" />
            <rect x="9" y="16" width="6" height="4" rx="1" />
            <path d="M6 8v3h12V8M12 11v5" />
        </>
    ),
} satisfies Record<string, ReactNode>;

type CanonicalIconName = keyof typeof iconNodes;

const contentIconAliases: Record<ContentIconName, CanonicalIconName> = {
    settings: "gear",
    security: "shield",
    shield_lock: "shield-lock",
    verified: "badge-check",
    fingerprint: "fingerprint",
    videocam: "camera",
    lan: "network",
    dns: "server",
    wifi: "wifi",
    storage: "hard-drive",
    backup: "cloud-arrow-up",
    cloud_done: "cloud-check",
    monitoring: "monitor-pulse",
    query_stats: "chart",
    speed: "gauge",
    business: "briefcase",
    apartment: "apartment",
    storefront: "storefront",
    warehouse: "warehouse",
    manufacturing: "factory",
    directions_car: "car",
    door_front: "door",
    toll: "gate",
    support_agent: "headset",
    location_on: "map-pin",
    call: "phone",
    mail: "mail",
    history: "history",
    architecture: "draft",
    schema: "workflow",
    hub: "hub",
    dynamic_feed: "feed",
    settings_input_component: "sliders",
};

const legacyIconAliases: Record<string, CanonicalIconName> = {
    analytics: "chart",
    arrow_forward: "arrow-right",
    badge: "id-badge",
    battery_charging_full: "battery",
    check_circle: "check-circle",
    chevron_left: "chevron-left",
    chevron_right: "chevron-right",
    deployed_code: "brackets-square",
    expand_more: "chevron-down",
    memory: "cpu",
    monitor_heart: "monitor-pulse",
    schedule: "clock",
    settings_ethernet: "network",
    settings_overscan: "scan",
    trending_up: "chart-up",
    verified_user: "badge-check",
    visibility: "eye",
    vpn_key: "key",
    warning: "triangle-alert",
};

function normalizeIconName(name: string): CanonicalIconName {
    const normalized = name.trim().toLowerCase().replaceAll(" ", "_");

    if (normalized in contentIconAliases) {
        return contentIconAliases[normalized as ContentIconName];
    }

    if (normalized in legacyIconAliases) {
        return legacyIconAliases[normalized];
    }

    if (normalized in iconNodes) {
        return normalized as CanonicalIconName;
    }

    return "circle-help";
}

export function isSupportedContentIcon(name: string): name is ContentIconName {
    return CONTENT_ICON_NAMES.includes(name as ContentIconName);
}

export default function Icon({ name, className, title }: IconProps) {
    const label = title || name.replaceAll("_", " ").replaceAll("-", " ");
    const canonicalName = normalizeIconName(name);

    return (
        <span
            aria-hidden={title ? undefined : "true"}
            aria-label={title ? label : undefined}
            className={clsx(
                "inline-flex size-[1em] shrink-0 items-center justify-center align-middle",
                className,
            )}
            title={title}
        >
            <svg
                className="h-full w-full"
                role={title ? "img" : undefined}
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                {title ? <title>{label}</title> : null}
                <g {...strokeProps}>{iconNodes[canonicalName]}</g>
            </svg>
        </span>
    );
}
