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
