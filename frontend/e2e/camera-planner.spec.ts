import { expect, test, type Locator } from "@playwright/test";
import { readFile } from "node:fs/promises";

const locales = [
    { prefix: "", lang: "ka-GE", title: "კამერების განლაგების პლანერი", add: "კამერის დამატება", area: "საკონტროლო არე", finish: "არეის დასრულება", coverage: "არეის დაფარვა", empty: "სამუშაო სივრცე მზადაა" },
    { prefix: "/en", lang: "en-GE", title: "CCTV camera layout planner", add: "Add camera", area: "Inspection area", finish: "Finish area", coverage: "Area coverage", empty: "Your workspace is ready" },
    { prefix: "/ru", lang: "ru-GE", title: "Планировщик размещения камер", add: "Добавить камеру", area: "Зона контроля", finish: "Завершить зону", coverage: "Покрытие зоны", empty: "Рабочая область готова" },
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
        const installationLink = page.getByRole("link", {
            name: {
                ka: "უსაფრთხოების კამერების მონტაჟი და გამართვა",
                en: "Security camera installation and setup",
                ru: "Монтаж и настройка камер видеонаблюдения",
            }[locale.lang.slice(0, 2) as "ka" | "en" | "ru"],
        });
        await expect(installationLink).toHaveAttribute(
            "href", locale.prefix + "/services/security-camera-installation",
        );

        const canvas = page.locator("canvas[role='img']");
        await expect(canvas).toBeVisible();
        await expect(page.getByText(locale.empty, { exact: true })).toBeVisible();
        await page.getByRole("button", { name: locale.add }).click();
        await place(canvas, 0.3, 0.4);
        await expect(page.getByText(locale.empty, { exact: true })).toHaveCount(0);
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


test("AI plan requires opt-in, suggests edit-ready geometry and preserves measured scale", async ({ page }) => {
    await page.route("**/api/camera-plans/vision", async (route) => {
        const body = route.request().postDataBuffer();
        expect(body?.toString("utf8")).toContain('name="ai_consent"');
        expect(body?.toString("utf8")).toContain('name="image"');
        await route.fulfill({
            status: 200,
            contentType: "application/json",
            body: JSON.stringify({
                data: {
                    image_type: "floor_plan", confidence: "medium",
                    summary: "Approximate placement only", caution: "Verify onsite",
                    scale_confirmed: true, suggested_count: 1,
                    rooms: [{ name: "Entrance hall", polygon: [{ x: 100, y: 100 }, { x: 900, y: 100 }, { x: 900, y: 900 }] }],
                    walls: [{ ax: 100, ay: 100, bx: 900, by: 100 }],
                    area: [{ x: 100, y: 100 }, { x: 900, y: 100 }, { x: 900, y: 900 }, { x: 100, y: 900 }],
                    cameras: [{ x: 500, y: 500, direction: 90, kind: "bullet", reason: "Front entrance" }],
                    notes: ["Choose real lens angle"],
                },
            }),
        });
    });
    await page.goto("/en/camera-planner");
    const png = Buffer.from("iVBORw0KGgoAAAANSUhEUgAAAAQAAAAECAIAAAAmkwkpAAAAFElEQVR4nGM8ceIEAwwwMSAB3BwAdjQCYKaF12gAAAAASUVORK5CYII=", "base64");
    await page.locator('input[type="file"][accept*="image/png"]').setInputFiles({
        name: "simple.png", mimeType: "image/png", buffer: png,
    });
    const ai = page.getByRole("button", { name: "Suggest cameras with AI" });
    await expect(ai).toBeDisabled();
    await page.getByText("I agree to send my uploaded plan/photo to OpenAI", { exact: false }).locator("..").locator('input[type="checkbox"]').check();
    await page.getByText("I measured the actual plan width", { exact: false }).locator("..").locator('input[type="checkbox"]').check();
    await expect(ai).toBeEnabled();
    await ai.click();
    await expect(page.getByText("Approximate placement only")).toBeVisible();
    await expect(page.getByText("Front entrance")).toBeVisible();
    await page.getByRole("button", { name: "Apply suggested layout" }).click();

    const downloading = page.waitForEvent("download");
    await page.getByRole("button", { name: "Download JSON" }).click();
    const file = await downloading;
    const design = JSON.parse(await readFile(await file.path(), "utf8"));
    expect(design.layout.cameras).toHaveLength(1);
    expect(design.layout.walls).toHaveLength(1);
    expect(design.layout.rooms).toHaveLength(1);
    expect(design.layout.area).toHaveLength(4);
    // Square uploaded image is letterboxed into 900x600 canvas: midpoint stays centered.
    expect(design.layout.cameras[0].x).toBe(450);
    expect(design.layout.cameras[0].y).toBe(300);

    // Real Laravel submission confirms uploaded photo conversion is CSP-safe.
    const quoteForm = page.locator("aside form");
    await quoteForm.locator("input").nth(0).fill("AI vision QA");
    await quoteForm.locator("input").nth(1).fill("Automated QA");
    await quoteForm.locator("input").nth(2).fill("+995555001235");
    await quoteForm.locator('input[type="checkbox"]').check();
    const savedQuote = page.waitForResponse((res) => res.url().endsWith("/api/camera-plans")
        && res.request().method() === "POST");
    await quoteForm.locator('button[type="submit"]').click();
    expect((await savedQuote).status()).toBe(201);

    await page.getByRole("button", { name: "Undo" }).click();

    const secondDownload = page.waitForEvent("download");
    await page.getByRole("button", { name: "Download JSON" }).click();
    const prior = JSON.parse(await readFile(await (await secondDownload).path(), "utf8"));
    expect(prior.layout.cameras).toHaveLength(0);
});
