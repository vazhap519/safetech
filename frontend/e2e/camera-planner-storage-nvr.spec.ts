import { expect, test } from "@playwright/test";

test("camera plan calculates grouped storage and validates NVR limits", async ({ page }) => {
    await page.goto("/en/camera-planner");
    const consentBanner = page.locator(".marketing-consent-banner");
    if (await consentBanner.isVisible()) {
        await consentBanner.getByRole("button").last().click();
        await expect(consentBanner).toBeHidden();
    }

    const canvas = page.getByRole("img", { name: /camera layout planner/i });
    const box = await canvas.boundingBox();
    expect(box).not.toBeNull();
    await canvas.click({ position: { x: box!.width * 0.3, y: box!.height * 0.4 } });
    await canvas.click({ position: { x: box!.width * 0.7, y: box!.height * 0.6 } });

    await expect(page.getByTestId("planner-storage")).toContainText("TB");
    await expect(page.getByTestId("planner-nvr-status")).toContainText("NVR is compatible");

    await page.getByLabel("NVR channels").fill("1");
    await expect(page.getByTestId("planner-nvr-status")).toContainText("channels");
});
