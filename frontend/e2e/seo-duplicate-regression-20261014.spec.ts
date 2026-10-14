import { expect, test } from "@playwright/test";

// Regression coverage for legacy indexed service URLs and malformed query links.
// Redirects should never create independently indexable duplicate pages.
const canonicalCases = [
  ["ip-camera-installation", "security-camera-installation"],
  ["video-surveillance-system-installation", "security-camera-installation"],
  ["barrier-gate-setup", "barrier-gate-installation"],
  ["it-technical-support", "business-it-support"],
  ["intercom-installation", "intercom-access-control-installation"],
  ["access-control-system-installation", "intercom-access-control-installation"],
] as const;

for (const prefix of ["", "/en", "/ru"]) {
  test(`legacy local service aliases redirect permanently (${prefix || "ka"})`, async ({ request }) => {
    for (const [oldSlug, newSlug] of canonicalCases) {
      const response = await request.get(`${prefix}/services/${oldSlug}/bakuriani`, { maxRedirects: 0 });
      expect([301, 308]).toContain(response.status());
      const location = new URL(response.headers()["location"]!, "https://safetech.ge");
      expect(location.pathname).toBe(`${prefix}/services/${newSlug}/bakuriani`);
      expect(location.search).toBe("");
    }
  });

  test(`invalid and empty service parameters cannot create duplicate catalogs (${prefix || "ka"})`, async ({ request }) => {
    for (const query of ["service=", "service=%20", "service=%2Fbad", "service=bad_slug"]) {
      const response = await request.get(`${prefix}/services?${query}`, { maxRedirects: 0 });
      expect([301, 308]).toContain(response.status());
      const location = new URL(response.headers()["location"]!, "https://safetech.ge");
      expect(location.pathname).toBe(`${prefix}/services`);
      expect(location.searchParams.has("service")).toBe(false);
    }
  });
}
