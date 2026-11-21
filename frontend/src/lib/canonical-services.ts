export const CANONICAL_SERVICE_SLUG_LIST = [
    "security-camera-installation",
    "low-voltage-cabling",
    "barrier-gate-installation",
    "intercom-access-control-installation",
    "business-it-support",
    "network-installation-configuration",
    "operating-system-installation",
] as const;

export const CANONICAL_SERVICE_SLUGS: ReadonlySet<string> = new Set(
    CANONICAL_SERVICE_SLUG_LIST,
);

export const SERVICE_CANONICAL_ALIASES: Record<string, string> = {
    "access-control-installation": "intercom-access-control-installation",
    "access-control-system-installation": "intercom-access-control-installation",
    "alarm-system-installation": "intercom-access-control-installation",
    "backup-setup": "business-it-support",
    "barrier-gate-setup": "barrier-gate-installation",
    "cat6-cabling": "low-voltage-cabling",
    "communications-infrastructure": "low-voltage-cabling",
    "computer-cleaning-maintenance": "operating-system-installation",
    "computer-component-replacement": "operating-system-installation",
    "computer-component-upgrades": "operating-system-installation",
    "computer-network-diagnostics": "network-installation-configuration",
    "computer-peripheral-setup": "operating-system-installation",
    "computer-peripheral-troubleshooting": "operating-system-installation",
    "computer-preventive-maintenance": "operating-system-installation",
    "computer-setup-optimization": "operating-system-installation",
    "computer-upgrade-optimization": "operating-system-installation",
    "computers-workstations-setup": "business-it-support",
    "corporate-email-setup": "business-it-support",
    "custom-computer-build": "operating-system-installation",
    "data-backup-recovery": "business-it-support",
    "fiber-optic-commercial-network-installation": "low-voltage-cabling",
    "intercom-installation": "intercom-access-control-installation",
    "ip-camera-installation": "security-camera-installation",
    "it-technical-support": "business-it-support",
    "lan-installation": "network-installation-configuration",
    "lan-network-installation": "network-installation-configuration",
    "low-voltage-design-estimation": "low-voltage-cabling",
    "mac-app-installation": "operating-system-installation",
    "mac-backup-migration": "operating-system-installation",
    "mac-data-recovery-backup": "operating-system-installation",
    "mac-diagnostics": "operating-system-installation",
    "mac-diagnostics-repair": "operating-system-installation",
    "mac-printer-wifi-setup": "operating-system-installation",
    "mac-software-installation": "operating-system-installation",
    "mac-software-setup": "operating-system-installation",
    "macbook-imac-software-setup": "operating-system-installation",
    "macos-installation": "operating-system-installation",
    "macos-installation-configuration": "operating-system-installation",
    "macos-update-optimization": "operating-system-installation",
    "microsoft-365-migration": "business-it-support",
    "microsoft-365-setup-migration": "business-it-support",
    "network-cable-installation": "low-voltage-cabling",
    "network-diagnostics": "network-installation-configuration",
    "network-printer-scanner-setup": "business-it-support",
    "network-rack-installation": "low-voltage-cabling",
    "new-computer-setup": "operating-system-installation",
    "office-digital-infrastructure": "business-it-support",
    "office-it-infrastructure": "business-it-support",
    "onsite-computer-service": "business-it-support",
    "os-installation-support": "operating-system-installation",
    "patch-panel-installation": "low-voltage-cabling",
    "patch-panel-network-outlet-installation": "low-voltage-cabling",
    "personal-computer-assembly": "operating-system-installation",
    "pos-system-installation": "business-it-support",
    "rack-assembly-cable-management": "low-voltage-cabling",
    "ram-upgrade": "operating-system-installation",
    "remote-it-support": "business-it-support",
    "rj45-connector-installation": "low-voltage-cabling",
    "router-wifi-configuration": "network-installation-configuration",
    "server-room-installation": "low-voltage-cabling",
    "software-installation": "operating-system-installation",
    "structured-cabling": "low-voltage-cabling",
    "structured-cabling-installation": "low-voltage-cabling",
    "telecommunications-infrastructure-installation": "low-voltage-cabling",
    "video-surveillance-system-installation": "security-camera-installation",
    "virus-removal-optimization": "operating-system-installation",
    "wifi-network-installation": "network-installation-configuration",
    "workstation-setup": "business-it-support",
};

export function resolveCanonicalServiceSlug(slug?: string | null): string | null {
    const normalized = slug?.trim() ?? "";

    return (
        SERVICE_CANONICAL_ALIASES[normalized] ||
        (CANONICAL_SERVICE_SLUGS.has(normalized) ? normalized : null)
    );
}

export function isCanonicalServiceSlug(slug?: string | null): boolean {
    return CANONICAL_SERVICE_SLUGS.has(slug?.trim() ?? "");
}
