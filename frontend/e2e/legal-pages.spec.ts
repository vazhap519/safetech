import { expect, test } from "@playwright/test";

test("privacy and terms pages are public and linked from the footer", async ({ page }) => {
    await page.goto("/privacy");
    await expect(
        page.getByRole("heading", { name: "კონფიდენციალურობის პოლიტიკა", level: 1 }),
    ).toBeVisible();
    await expect(page.getByRole("heading", { name: "1. ზოგადი ინფორმაცია", level: 2 })).toBeVisible();

    const termsLink = page.getByRole("link", { name: /პირობები|Terms|Условия/i });
    await expect(termsLink).toBeVisible();

    await page.goto("/terms");
    await expect(
        page.getByRole("heading", { name: "მომსახურების პირობები", level: 1 }),
    ).toBeVisible();
    await expect(page.getByRole("heading", { name: "1. პირობების მიღება", level: 2 })).toBeVisible();
});

test("legal pages localize on locale routes", async ({ page }) => {
    await page.goto("/en/privacy");
    await expect(page.getByRole("heading", { name: "Privacy Policy", level: 1 })).toBeVisible();

    await page.goto("/ru/terms");
    await expect(page.getByRole("heading", { name: "Условия использования", level: 1 })).toBeVisible();
});
