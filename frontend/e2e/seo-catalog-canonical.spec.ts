import { expect, test } from "@playwright/test";

const legacySlugs = [
  "ip-camera-installation",
  "video-surveillance-system-installation",
  "barrier-gate-setup",
  "it-technical-support",
  "intercom-installation",
  "access-control-system-installation",
];

for (const prefix of ["", "/en", "/ru"]) {
  test(`service catalog excludes legacy duplicate links and self-canonicalizes (${prefix || "ka"})`, async ({ page }) => {
    const response = await page.goto(`${prefix}/services`, { waitUntil: "domcontentloaded" });
    expect(response?.status()).toBe(200);
    const canonical = page.locator('link[rel="canonical"]');
    await expect(canonical).toHaveCount(1);
    const href = await canonical.getAttribute("href");
    expect(new URL(href!, "https://safetech.ge").pathname).toBe(`${prefix}/services`);
    for (const slug of legacySlugs) {
      await expect(page.locator(`a[href*="/services/${slug}"]`)).toHaveCount(0);
    }
    await expect(page.locator('a[href*="/services?service="]')).toHaveCount(0);
  });
}
