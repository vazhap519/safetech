import { expect, test } from "@playwright/test";

// QA-only project populated by the Release Candidate workflow with one card
// per section and an intentionally overlong legacy CMS result value.
for (const viewport of [
    { width: 1440, height: 900 },
    { width: 390, height: 844 },
]) {
    test(`project detail remains readable with sparse cards and legacy metrics at ${viewport.width}px`, async ({ page }) => {
        await page.setViewportSize(viewport);
        const response = await page.goto("/projects/qa-release-project", {
            waitUntil: "domcontentloaded",
        });
        expect(response?.status()).toBe(200);

        const resultCard = page
            .getByRole("heading", { name: "Project results", exact: true })
            .locator("xpath=ancestor::article[1]");
        await expect(resultCard).toBeVisible();
        await expect(resultCard.locator("li")).toHaveCount(3);
        await expect(resultCard.locator("strong")).toHaveCount(0);

        const specValue = page.getByText("13 cameras; 10 initial; 3 additional");
        await expect(specValue).toBeVisible();
        const specFont = await specValue.evaluate((node) =>
            Number.parseFloat(window.getComputedStyle(node).fontSize),
        );
        expect(specFont).toBeLessThanOrEqual(20);

        for (const heading of ["Project challenge", "Project solution", "Installation"]) {
            await expect(
                page.getByRole("heading", { name: heading, exact: true }),
            ).toBeVisible();
        }

        const { width, right, viewportWidth } = await resultCard.evaluate((node) => {
            const rect = node.getBoundingClientRect();
            return {
                width: rect.width,
                right: rect.right,
                viewportWidth: window.innerWidth,
            };
        });

        expect(right).toBeLessThanOrEqual(viewportWidth + 2);
        if (viewport.width >= 1024) {
            expect(width).toBeGreaterThan(500);
        }
    });
}
