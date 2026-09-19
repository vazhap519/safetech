import { expect, test, type Locator } from "@playwright/test";
import { readFile } from "node:fs/promises";

const locales = [
    { prefix: "", lang: "ka-GE", title: "კამერების განლაგების პლანერი", add: "კამერის დამატება", area: "საკონტროლო არე", finish: "არის დასრულება", coverage: "არეის დაფარვა" },
    { prefix: "/en", lang: "en-GE", title: "CCTV camera layout planner", add: "Add camera", area: "Inspection area", finish: "Finish area", coverage: "Area coverage" },
    { prefix: "/ru", lang: "ru-GE", title: "Планировщик размещения камер", add: "Добавить камеру", area: "Зона контроля", finish: "Завершить зону", coverage: "Покрытие зоны" },
];

async function place(canvas: Locator, x: number, y: number) {
    const box = await canvas.boundingBox();
    expect(box).not.toBeNull();
    await canvas.click({ position: { x: box!.width * x, y: box!.height * y } });
}

for (const locale of locales) {
    test((locale.prefix || "/") + " CCTV layout, blind spots, export and Laravel quote submission", async ({ page }) => {
        const response = await page.goto(locale.prefix + "/camera-planner", { waitUntil: "domcontentloaded" });
        expect(response?.status()).toBe(200);
        await expect(page.locator("html")).toHaveAttribute("lang", locale.lang);
        await expect(page.getByRole("heading", { level: 1, name: locale.title })).toHaveCount(1);

        const canvas = page.locator("canvas[role='img']");
        await expect(canvas).toBeVisible();
        await page.getByRole("button", { name: locale.add }).click();
        await place(canvas, 0.3, 0.4);
        await expect(page.getByText(locale.coverage, { exact: true })).toBeVisible();

        await page.getByRole("button", { name: locale.area }).click();
        for (const point of [[.1, .1], [.88, .1], [.88, .88], [.1, .88]]) {
            await place(canvas, point[0], point[1]);
        }
        await page.getByRole("button", { name: locale.finish }).click();
        const percentage = page.getByText(locale.coverage, { exact: true }).locator("..").locator("strong");
        await expect(percentage).toContainText("%");

        const downloadPromise = page.waitForEvent("download");
        await page.locator('button[type="button"]').filter({ hasText: /JSON/ }).first().click();
        const saved = await downloadPromise;
        expect(saved.suggestedFilename()).toBe("safetech-camera-plan.json");
        const design = JSON.parse(await readFile(await saved.path(), "utf8"));
        expect(design.layout.cameras).toHaveLength(1);
        expect(design.layout.area).toHaveLength(4);
        expect(design.layout.version).toBe(1);

        const form = page.locator("aside form");
        await form.locator("input").nth(0).fill("QA Planner Test");
        await form.locator("input").nth(1).fill("Automated QA");
        await form.locator("input").nth(2).fill("+995555001234");
        await form.locator('input[type="checkbox"]').check();
        const submit = page.waitForResponse((r) => r.url().includes("/api/camera-plans") && r.request().method() === "POST");
        await form.locator('button[type="submit"]').click();
        expect((await submit).status()).toBe(201);
        await expect(form.locator('[role="status"]')).not.toBeEmpty();
    });
}
