import { expect, test } from "@playwright/test";

const legacySlugs = new Set([
    "ip-camera-installation",
    "video-surveillance-system-installation",
    "barrier-gate-setup",
    "it-technical-support",
    "intercom-installation",
    "access-control-system-installation",
]);

for (const prefix of ["", "/en", "/ru"]) {
    const locale = prefix || "ka";

    test(`${locale} service catalog uses one clean self-canonical URL and no legacy service links`, async ({ page }) => {
        const path = `${prefix}/services`;
        const response = await page.goto(path, { waitUntil: "domcontentloaded" });
        expect(response?.status()).toBe(200);

        const canonical = page.locator('link[rel="canonical"]');
        await expect(canonical).toHaveCount(1);
        const canonicalHref = await canonical.getAttribute("href");
        expect(canonicalHref, "canonical must have a nonempty href").toBeTruthy();

        const canonicalUrl = new URL(canonicalHref!, page.url());
        const currentUrl = new URL(page.url());
        expect(canonicalUrl.origin).toBe(currentUrl.origin);
        expect(canonicalUrl.pathname).toBe(path);
        expect(canonicalUrl.search).toBe("");
        expect(canonicalUrl.hash).toBe("");

        const serviceLinks = await page.locator('a[href]').evaluateAll((anchors) =>
            anchors.map((anchor) => (anchor as HTMLAnchorElement).href),
        );

        for (const href of serviceLinks) {
            const url = new URL(href);
            if (url.origin !== currentUrl.origin) continue;

            const segments = url.pathname.split("/").filter(Boolean);
            const serviceIndex = segments[0] === "en" || segments[0] === "ru" ? 1 : 0;
            if (segments[serviceIndex] !== "services") continue;

            expect(
                url.searchParams.has("service"),
                `${locale} catalog links to a legacy query URL: ${href}`,
            ).toBe(false);

            const slug = segments[serviceIndex + 1];
            expect(
                legacySlugs.has(slug),
                `${locale} catalog links to legacy service slug: ${href}`,
            ).toBe(false);
        }
    });
}
