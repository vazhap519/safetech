import clsx from "clsx";

type IconProps = {
    name: string;
    className?: string;
    title?: string;
};

const strokeProps = {
    fill: "none",
    stroke: "currentColor",
    strokeLinecap: "round" as const,
    strokeLinejoin: "round" as const,
    strokeWidth: 1.8,
};

const iconAliases: Record<string, string> = {
    analytics: "chart",
    apartment: "building",
    architecture: "draft",
    arrow_forward: "arrow-right",
    backup: "cloud-arrow-up",
    badge: "id-badge",
    battery_charging_full: "battery",
    business: "building",
    call: "phone",
    check_circle: "check-circle",
    chevron_left: "chevron-left",
    chevron_right: "chevron-right",
    cloud_done: "cloud-check",
    deployed_code: "brackets-square",
    directions_car: "car",
    dns: "server",
    door_front: "door",
    dynamic_feed: "share-nodes",
    expand_more: "chevron-down",
    fingerprint: "fingerprint",
    history: "history",
    hub: "share-nodes",
    lan: "network",
    location_on: "map-pin",
    mail: "mail",
    manufacturing: "factory",
    memory: "cpu",
    monitor_heart: "monitor-pulse",
    monitoring: "monitor-pulse",
    query_stats: "chart",
    schedule: "clock",
    schema: "share-nodes",
    security: "shield",
    settings: "gear",
    settings_ethernet: "network",
    settings_input_component: "sliders",
    settings_overscan: "scan",
    shield_lock: "shield-lock",
    storefront: "storefront",
    support_agent: "headset",
    speed: "gauge",
    storage: "hard-drive",
    toll: "gate",
    trending_up: "chart-up",
    verified: "badge-check",
    verified_user: "badge-check",
    videocam: "camera",
    visibility: "eye",
    vpn_key: "key",
    warehouse: "warehouse",
    warning: "triangle-alert",
    wifi: "wifi",
};

function renderIcon(name: string) {
    switch (iconAliases[name] ?? name) {
        case "arrow-right":
            return (
                <>
                    <path {...strokeProps} d="M5 12h14" />
                    <path {...strokeProps} d="m13 5 7 7-7 7" />
                </>
            );
        case "badge-check":
            return (
                <>
                    <path
                        {...strokeProps}
                        d="M12 3.5 15 5l3.4-.1.1 3.4L20.5 12 18.5 15.7l-.1 3.4-3.4-.1L12 20.5 8.9 19l-3.4.1-.1-3.4L3.5 12 5.5 8.3l.1-3.4L9 5l3-1.5Z"
                    />
                    <path {...strokeProps} d="m8.5 12 2.3 2.3 4.7-4.8" />
                </>
            );
        case "battery":
            return (
                <>
                    <rect {...strokeProps} x="3" y="7" width="17" height="10" rx="2" />
                    <path {...strokeProps} d="M20 10h1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-1" />
                    <path {...strokeProps} d="m10 9-2 4h2l-1 3 5-6h-2l1-2" />
                </>
            );
        case "brackets-square":
            return (
                <>
                    <path {...strokeProps} d="M9 5H6a1 1 0 0 0-1 1v3" />
                    <path {...strokeProps} d="M15 5h3a1 1 0 0 1 1 1v3" />
                    <path {...strokeProps} d="M9 19H6a1 1 0 0 1-1-1v-3" />
                    <path {...strokeProps} d="M15 19h3a1 1 0 0 0 1-1v-3" />
                    <path {...strokeProps} d="m10 9-2 3 2 3" />
                    <path {...strokeProps} d="m14 9 2 3-2 3" />
                </>
            );
        case "building":
            return (
                <>
                    <path {...strokeProps} d="M4 21V5a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v16" />
                    <path {...strokeProps} d="M16 21v-9a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v9" />
                    <path {...strokeProps} d="M8 8h4M8 12h4M8 16h4" />
                </>
            );
        case "camera":
            return (
                <>
                    <rect {...strokeProps} x="3" y="7" width="14" height="10" rx="2" />
                    <path {...strokeProps} d="m17 10 4-2v8l-4-2" />
                    <circle {...strokeProps} cx="10" cy="12" r="2.5" />
                </>
            );
        case "car":
            return (
                <>
                    <path {...strokeProps} d="m5 16 1-5a2 2 0 0 1 2-1.5h8a2 2 0 0 1 2 1.5l1 5" />
                    <path {...strokeProps} d="M4 16h16v2a1 1 0 0 1-1 1h-1v-1a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v1H5a1 1 0 0 1-1-1v-2Z" />
                    <circle {...strokeProps} cx="7.5" cy="15.5" r="1" />
                    <circle {...strokeProps} cx="16.5" cy="15.5" r="1" />
                </>
            );
        case "chart":
            return (
                <>
                    <path {...strokeProps} d="M4 20h16" />
                    <path {...strokeProps} d="M6 17V9" />
                    <path {...strokeProps} d="M12 17V5" />
                    <path {...strokeProps} d="M18 17v-6" />
                </>
            );
        case "chart-up":
            return (
                <>
                    <path {...strokeProps} d="M4 19h16" />
                    <path {...strokeProps} d="m6 15 4-4 3 3 5-6" />
                    <path {...strokeProps} d="M18 8h-3V5" />
                </>
            );
        case "check-circle":
            return (
                <>
                    <circle {...strokeProps} cx="12" cy="12" r="8.5" />
                    <path {...strokeProps} d="m8.5 12 2.4 2.4 4.8-4.8" />
                </>
            );
        case "chevron-down":
            return <path {...strokeProps} d="m6 9 6 6 6-6" />;
        case "chevron-left":
            return <path {...strokeProps} d="m15 6-6 6 6 6" />;
        case "chevron-right":
            return <path {...strokeProps} d="m9 6 6 6-6 6" />;
        case "clock":
            return (
                <>
                    <circle {...strokeProps} cx="12" cy="12" r="8.5" />
                    <path {...strokeProps} d="M12 7.5v5l3 2" />
                </>
            );
        case "cloud-arrow-up":
            return (
                <>
                    <path {...strokeProps} d="M7 18a4 4 0 0 1-.6-8A5.5 5.5 0 0 1 17 8.6 4 4 0 1 1 18 18Z" />
                    <path {...strokeProps} d="m12 15.5V10" />
                    <path {...strokeProps} d="m9.5 12.5 2.5-2.5 2.5 2.5" />
                </>
            );
        case "cloud-check":
            return (
                <>
                    <path {...strokeProps} d="M7 18a4 4 0 0 1-.6-8A5.5 5.5 0 0 1 17 8.6 4 4 0 1 1 18 18Z" />
                    <path {...strokeProps} d="m9.5 13 1.8 1.8 3.4-3.6" />
                </>
            );
        case "cpu":
            return (
                <>
                    <rect {...strokeProps} x="7" y="7" width="10" height="10" rx="2" />
                    <path {...strokeProps} d="M9.5 1.5v3M14.5 1.5v3M9.5 19.5v3M14.5 19.5v3M1.5 9.5h3M1.5 14.5h3M19.5 9.5h3M19.5 14.5h3" />
                    <path {...strokeProps} d="M10 10h4v4h-4z" />
                </>
            );
        case "door":
            return (
                <>
                    <path {...strokeProps} d="M6 21V5.5A1.5 1.5 0 0 1 7.5 4h7A1.5 1.5 0 0 1 16 5.5V21" />
                    <path {...strokeProps} d="M6 21h12" />
                    <circle {...strokeProps} cx="13" cy="12" r=".8" />
                </>
            );
        case "draft":
            return (
                <>
                    <path {...strokeProps} d="M4 18V6a2 2 0 0 1 2-2h12v12a2 2 0 0 1-2 2H4Z" />
                    <path {...strokeProps} d="M8 8h6M8 12h8M8 16h5" />
                    <path {...strokeProps} d="m18 4 2 2" />
                </>
            );
        case "eye":
            return (
                <>
                    <path {...strokeProps} d="M2.5 12s3.6-6 9.5-6 9.5 6 9.5 6-3.6 6-9.5 6-9.5-6-9.5-6Z" />
                    <circle {...strokeProps} cx="12" cy="12" r="2.5" />
                </>
            );
        case "factory":
            return (
                <>
                    <path {...strokeProps} d="M3 21V9l6 3V9l6 3V5l6-2v18H3Z" />
                    <path {...strokeProps} d="M7 21v-4h4v4M16 9h.01M16 13h.01M16 17h.01" />
                </>
            );
        case "fingerprint":
            return (
                <>
                    <path {...strokeProps} d="M9 8a3 3 0 1 1 6 0v2.5" />
                    <path {...strokeProps} d="M7.5 10.5V9A4.5 4.5 0 1 1 16.5 9v3" />
                    <path {...strokeProps} d="M6 12v-1a6 6 0 1 1 12 0v1.5" />
                    <path {...strokeProps} d="M9 14v1a3 3 0 0 0 6 0v-3" />
                    <path {...strokeProps} d="M12 18v2.5" />
                </>
            );
        case "gauge":
            return (
                <>
                    <path {...strokeProps} d="M4.5 17a8 8 0 1 1 15 0" />
                    <path {...strokeProps} d="m12 12 4-4" />
                    <circle {...strokeProps} cx="12" cy="12" r="1" />
                </>
            );
        case "gate":
            return (
                <>
                    <path {...strokeProps} d="M4 20V8h4v12M16 20V8h4v12M8 12h8M8 16h8" />
                    <path {...strokeProps} d="M12 4v16" />
                </>
            );
        case "gear":
            return (
                <>
                    <path {...strokeProps} d="M12 8.5A3.5 3.5 0 1 1 8.5 12 3.5 3.5 0 0 1 12 8.5Z" />
                    <path {...strokeProps} d="m12 2 1 2.3 2.6.5.6 2.6 2.2 1.3-.8 2.5.8 2.5-2.2 1.3-.6 2.6-2.6.5L12 22l-1-2.3-2.6-.5-.6-2.6-2.2-1.3.8-2.5-.8-2.5 2.2-1.3.6-2.6 2.6-.5L12 2Z" />
                </>
            );
        case "hard-drive":
            return (
                <>
                    <rect {...strokeProps} x="4" y="5" width="16" height="14" rx="2" />
                    <path {...strokeProps} d="M7 15h10" />
                    <path {...strokeProps} d="M8.5 10h7" />
                    <circle {...strokeProps} cx="16.5" cy="15.5" r=".8" />
                </>
            );
        case "headset":
            return (
                <>
                    <path {...strokeProps} d="M4 13a8 8 0 1 1 16 0" />
                    <rect {...strokeProps} x="3" y="12" width="3.5" height="6" rx="1.5" />
                    <rect {...strokeProps} x="17.5" y="12" width="3.5" height="6" rx="1.5" />
                    <path {...strokeProps} d="M18 18a3 3 0 0 1-3 3h-1" />
                </>
            );
        case "history":
            return (
                <>
                    <path {...strokeProps} d="M4.5 7.5H8V4" />
                    <path {...strokeProps} d="M7.8 7.8A7.5 7.5 0 1 1 4.5 12" />
                    <path {...strokeProps} d="M12 8v4l2.5 1.5" />
                </>
            );
        case "id-badge":
            return (
                <>
                    <rect {...strokeProps} x="4" y="4.5" width="16" height="15" rx="2.5" />
                    <circle {...strokeProps} cx="9" cy="10" r="2" />
                    <path {...strokeProps} d="M6.8 15a3.6 3.6 0 0 1 4.4 0" />
                    <path {...strokeProps} d="M14.5 9.5h3.5M14.5 13h3.5M14.5 16.5h2.5" />
                </>
            );
        case "key":
            return (
                <>
                    <circle {...strokeProps} cx="8.5" cy="12" r="3.5" />
                    <path {...strokeProps} d="M12 12h8" />
                    <path {...strokeProps} d="M16 12v2M18 12v2" />
                </>
            );
        case "mail":
            return (
                <>
                    <rect {...strokeProps} x="3" y="5" width="18" height="14" rx="2" />
                    <path {...strokeProps} d="m4 7 8 6 8-6" />
                </>
            );
        case "map-pin":
            return (
                <>
                    <path {...strokeProps} d="M12 21s6-5.7 6-11a6 6 0 1 0-12 0c0 5.3 6 11 6 11Z" />
                    <circle {...strokeProps} cx="12" cy="10" r="2" />
                </>
            );
        case "monitor-pulse":
            return (
                <>
                    <rect {...strokeProps} x="3" y="5" width="18" height="12" rx="2" />
                    <path {...strokeProps} d="M8 21h8M12 17v4" />
                    <path {...strokeProps} d="m6 11 2.5 0 1.5-3 2.5 8 1.5-4 3 0" />
                </>
            );
        case "network":
            return (
                <>
                    <path {...strokeProps} d="M12 5v4M6 19v-3M18 19v-3" />
                    <rect {...strokeProps} x="9" y="3" width="6" height="4" rx="1" />
                    <rect {...strokeProps} x="3" y="16" width="6" height="4" rx="1" />
                    <rect {...strokeProps} x="15" y="16" width="6" height="4" rx="1" />
                    <path {...strokeProps} d="M12 7v4M12 11H6v5M12 11h6v5" />
                </>
            );
        case "phone":
            return (
                <>
                    <path {...strokeProps} d="M6.5 4.5h3L11 8 9.5 10.2a13.5 13.5 0 0 0 4.3 4.3L16 13l3.5 1.5v3a2 2 0 0 1-2 2A15 15 0 0 1 4.5 6.5a2 2 0 0 1 2-2Z" />
                </>
            );
        case "scan":
            return (
                <>
                    <path {...strokeProps} d="M8 4H5a1 1 0 0 0-1 1v3M16 4h3a1 1 0 0 1 1 1v3M8 20H5a1 1 0 0 1-1-1v-3M16 20h3a1 1 0 0 0 1-1v-3" />
                    <path {...strokeProps} d="M7 12h10" />
                </>
            );
        case "server":
            return (
                <>
                    <rect {...strokeProps} x="4" y="4" width="16" height="6" rx="1.5" />
                    <rect {...strokeProps} x="4" y="14" width="16" height="6" rx="1.5" />
                    <path {...strokeProps} d="M8 7h.01M12 7h.01M8 17h.01M12 17h.01" />
                </>
            );
        case "share-nodes":
            return (
                <>
                    <circle {...strokeProps} cx="6" cy="12" r="2" />
                    <circle {...strokeProps} cx="18" cy="7" r="2" />
                    <circle {...strokeProps} cx="18" cy="17" r="2" />
                    <path {...strokeProps} d="m7.7 11 8.6-3M7.7 13l8.6 3" />
                </>
            );
        case "shield":
            return (
                <>
                    <path {...strokeProps} d="M12 3.5 18 6v5.6c0 4-2.5 7.7-6 8.9-3.5-1.2-6-4.9-6-8.9V6l6-2.5Z" />
                </>
            );
        case "shield-lock":
            return (
                <>
                    <path {...strokeProps} d="M12 3.5 18 6v5.6c0 4-2.5 7.7-6 8.9-3.5-1.2-6-4.9-6-8.9V6l6-2.5Z" />
                    <rect {...strokeProps} x="9.2" y="11" width="5.6" height="4.4" rx="1" />
                    <path {...strokeProps} d="M10.5 11v-1a1.5 1.5 0 1 1 3 0v1" />
                </>
            );
        case "sliders":
            return (
                <>
                    <path {...strokeProps} d="M5 7h14M5 12h14M5 17h14" />
                    <circle {...strokeProps} cx="9" cy="7" r="1.2" />
                    <circle {...strokeProps} cx="15" cy="12" r="1.2" />
                    <circle {...strokeProps} cx="11" cy="17" r="1.2" />
                </>
            );
        case "storefront":
            return (
                <>
                    <path {...strokeProps} d="M4 8h16l-1 4a2 2 0 0 1-2 1.5H7A2 2 0 0 1 5 12L4 8Z" />
                    <path {...strokeProps} d="M6 8V6a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2M6 13.5V20h12v-6.5" />
                    <path {...strokeProps} d="M10 20v-4h4v4" />
                </>
            );
        case "triangle-alert":
            return (
                <>
                    <path {...strokeProps} d="M12 4 20 19H4L12 4Z" />
                    <path {...strokeProps} d="M12 9v4M12 16h.01" />
                </>
            );
        case "warehouse":
            return (
                <>
                    <path {...strokeProps} d="M3 10 12 4l9 6v10H3V10Z" />
                    <path {...strokeProps} d="M8 20v-6h8v6M7 10h10" />
                </>
            );
        case "wifi":
            return (
                <>
                    <path {...strokeProps} d="M5 9a10 10 0 0 1 14 0" />
                    <path {...strokeProps} d="M8 12a6 6 0 0 1 8 0" />
                    <path {...strokeProps} d="M10.5 14.5a2.5 2.5 0 0 1 3 0" />
                    <circle {...strokeProps} cx="12" cy="18" r=".8" />
                </>
            );
        default:
            return (
                <>
                    <circle {...strokeProps} cx="12" cy="12" r="8.5" />
                    <path {...strokeProps} d="M12 8v4M12 16h.01" />
                </>
            );
    }
}

export default function Icon({ name, className, title }: IconProps) {
    const label = title || name.replaceAll("_", " ").replaceAll("-", " ");

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
                viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg"
            >
                {renderIcon(name)}
            </svg>
        </span>
    );
}
