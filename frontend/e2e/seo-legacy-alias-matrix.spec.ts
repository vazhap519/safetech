import { expect, test } from "@playwright/test";

const aliases = [
  ["ip-camera-installation", "security-camera-installation"],
  ["video-surveillance-system-installation", "security-camera-installation"],
  ["barrier-gate-setup", "barrier-gate-installation"],
  ["it-technical-support", "business-it-support"],
  ["intercom-installation", "intercom-access-control-installation"],
  ["access-control-system-installation", "intercom-access-control-installation"],
] as const;

for (const prefix of ["", "/en", "/ru"]) {
  test(`legacy service aliases resolve canonically (${prefix || "ka"})`, async ({ request }) => {
    for (const [alias, canonical] of aliases) {
      const response = await request.get(`${prefix}/services/${alias}`, { maxRedirects: 0 });
      expect([301, 308], `${prefix}/services/${alias} should permanently redirect`).toContain(response.status());
      const location = response.headers()["location"];
      expect(location).toBeTruthy();
      const target = new URL(location!, "https://safetech.ge");
      expect(target.pathname).toBe(`${prefix}/services/${canonical}`);
      expect(target.search).toBe("");
    }
  });

  test(`legacy query service links resolve canonically (${prefix || "ka"})`, async ({ request }) => {
    for (const [alias, canonical] of aliases) {
      const response = await request.get(`${prefix}/services?service=${alias}`, { maxRedirects: 0 });
      expect([301, 308]).toContain(response.status());
      const target = new URL(response.headers()["location"]!, "https://safetech.ge");
      expect(target.pathname).toBe(`${prefix}/services/${canonical}`);
      expect(target.search).toBe("");
    }
  });
}
