import { expect, test } from "@playwright/test";

test("camera plan calculates grouped storage and validates NVR limits", async ({ page }) => {
    await page.goto("/en/camera-planner");
    const canvas = page.getByRole("img", { name: /camera layout planner/i });
    await canvas.click({ position: { x: 180, y: 180 } });
    await canvas.click({ position: { x: 360, y: 260 } });

    await expect(page.getByTestId("planner-storage")).toContainText("TB");
    await expect(page.getByTestId("planner-nvr-status")).toContainText("NVR is compatible");

    await page.getByLabel("NVR channels").fill("1");
    await expect(page.getByTestId("planner-nvr-status")).toContainText("channels");
});
