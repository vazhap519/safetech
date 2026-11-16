import { expect, test } from "@playwright/test";

import { readFileSync } from "node:fs";
import { resolve } from "node:path";

// Use Laravel's authoritative registry so new aliases cannot silently escape QA.
const registry = readFileSync(
  resolve(process.cwd(), "../back/database/seeders/GoogleBusinessServiceDefinitions.php"),
  "utf8",
).split("public const CANONICAL_ALIASES = [")[1].split("];", 1)[0];
const aliases = [...registry.matchAll(/'([a-z0-9-]+)'\s*=>\s*'([a-z0-9-]+)'/g)]
  .map((match) => [match[1], match[2]] as const);

for (const prefix of ["", "/ka", "/en", "/ru"]) {
  test(`legacy service aliases resolve canonically (${prefix || "ka"})`, async ({ request }) => {
    for (const [alias, canonical] of aliases) {
      const response = await request.get(`${prefix}/services/${alias}`, { maxRedirects: 0 });
      expect([301, 308], `${prefix}/services/${alias} should permanently redirect`).toContain(response.status());
      const location = response.headers()["location"];
      expect(location).toBeTruthy();
      const target = new URL(location!, "https://safetech.ge");
      expect(target.pathname).toBe(`${prefix === "/ka" ? "" : prefix}/services/${canonical}`);
      expect(target.search).toBe("");
    }
  });

  test(`legacy query service links resolve canonically (${prefix || "ka"})`, async ({ request }) => {
    for (const [alias, canonical] of aliases) {
      const response = await request.get(`${prefix}/services?service=${alias}`, { maxRedirects: 0 });
      expect([301, 308]).toContain(response.status());
      const target = new URL(response.headers()["location"]!, "https://safetech.ge");
      expect(target.pathname).toBe(`${prefix === "/ka" ? "" : prefix}/services/${canonical}`);
      expect(target.search).toBe("");
    }
  });
}

for (const prefix of ["", "/ka", "/en", "/ru"]) {
  test(`all legacy city paths preserve location and query (${prefix || "ka"})`, async ({ request }) => {
    expect(aliases.length).toBeGreaterThan(50);
    for (const [alias, canonical] of aliases) {
      const response = await request.get(`${prefix}/services/${alias}/tbilisi?utm_source=regression`, { maxRedirects: 0 });
      expect(response.status()).toBe(308);
      const target = new URL(response.headers()["location"]!, "https://safetech.ge");
      expect(target.pathname).toBe(`${prefix === "/ka" ? "" : prefix}/services/${canonical}/tbilisi`);
      expect(target.search).toBe("?utm_source=regression");
    }
  });
}

for (const prefix of ["", "/ka", "/en", "/ru"]) {
  test(`legacy query service links preserve unrelated query parameters (${prefix || "ka"})`, async ({ request }) => {
    const response = await request.get(
      `${prefix}/services?service=ip-camera-installation&utm_source=regression&campaign=autumn`,
      { maxRedirects: 0 },
    );
    expect(response.status()).toBe(308);
    const target = new URL(response.headers()["location"]!, "https://safetech.ge");
    expect(target.pathname).toBe(`${prefix === "/ka" ? "" : prefix}/services/security-camera-installation`);
    expect(target.searchParams.get("service")).toBeNull();
    expect(target.searchParams.get("utm_source")).toBe("regression");
    expect(target.searchParams.get("campaign")).toBe("autumn");
  });

  test(`unknown service query routes to clean catalog (${prefix || "ka"})`, async ({ request }) => {
    const response = await request.get(
      `${prefix}/services?service=unknown-service-id&utm_source=regression`,
      { maxRedirects: 0 },
    );
    expect(response.status()).toBe(308);
    const target = new URL(response.headers()["location"]!, "https://safetech.ge");
    expect(target.pathname).toBe(`${prefix === "/ka" ? "" : prefix}/services`);
    expect(target.searchParams.get("service")).toBeNull();
    expect(target.searchParams.get("utm_source")).toBe("regression");
  });
}

const searchConsoleLegacyUrls = [
  ["/services/pos-system-installation/bakuriani", "/services/business-it-support/bakuriani"],
  ["/ru/services/computer-cleaning-maintenance/borjomi", "/ru/services/operating-system-installation/borjomi"],
  ["/en/services/fiber-optic-commercial-network-installation/abastumani", "/en/services/low-voltage-cabling/abastumani"],
  ["/ru/services/custom-computer-build/abastumani", "/ru/services/operating-system-installation/abastumani"],
  ["/en/services/mac-printer-wifi-setup/tbilisi", "/en/services/operating-system-installation/tbilisi"],
  ["/services/it-technical-support/tbilisi", "/services/business-it-support/tbilisi"],
  ["/en/services/barrier-gate-setup", "/en/services/barrier-gate-installation"],
  ["/en/services/wifi-network-installation", "/en/services/network-installation-configuration"],
  ["/ru/services?service=operating-system-installation", "/ru/services/operating-system-installation"],
  ["/services?service=custom-computer-build", "/services/operating-system-installation"],
  ["/services?service=ram-upgrade", "/services/operating-system-installation"],
  ["/ru/services?service=office-digital-infrastructure", "/ru/services/business-it-support"],
  ["/ru/services?service=computer-peripheral-troubleshooting", "/ru/services/operating-system-installation"],
  ["/ru/services?service=workstation-setup", "/ru/services/business-it-support"],
  ["/ru/services?service=access-control-system-installation", "/ru/services/intercom-access-control-installation"],
  ["/en/services?service=ram-upgrade", "/en/services/operating-system-installation"],
] as const;

test("Search Console November 2026 legacy URL samples redirect to their intended canonical paths", async ({ request }) => {
  for (const [legacy, canonical] of searchConsoleLegacyUrls) {
    const response = await request.get(legacy, { maxRedirects: 0 });
    expect([301, 308], legacy).toContain(response.status());
    const location = response.headers()["location"];
    expect(location, legacy).toBeTruthy();
    const target = new URL(location!, "https://safetech.ge");
    expect(target.pathname, legacy).toBe(canonical);
    expect(target.search, legacy).toBe("");
  }
});
