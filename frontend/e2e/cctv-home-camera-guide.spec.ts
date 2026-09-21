import { expect, test } from "@playwright/test";

const cases = [
    {
        prefix: "",
        lang: "ka-GE",
        title: "რამდენი უსაფრთხოების კამერა სჭირდება კერძო სახლს?",
        guideLink: "წაიკითხეთ კამერების დაგეგმვის გზამკვლევი",
        plannerLink: "გახსენით კამერების პლანერი",
    },
    {
        prefix: "/en",
        lang: "en-GE",
        title: "How many security cameras does a house need?",
        guideLink: "Read the home camera planning guide",
        plannerLink: "Open the camera planner",
    },
    {
        prefix: "/ru",
        lang: "ru-GE",
        title: "Сколько камер видеонаблюдения нужно для частного дома?",
        guideLink: "Читать руководство по размещению камер",
        plannerLink: "Открыть планировщик камер",
    },
] as const;

for (const item of cases) {
    test(item.lang + " practical CCTV guide has correct SEO and user journey", async ({ page }) => {
        const path = item.prefix + "/guides/how-many-cameras-for-a-house";
        const response = await page.goto(path, { waitUntil: "domcontentloaded" });
        expect(response?.status()).toBe(200);
        await expect(page.locator("html")).toHaveAttribute("lang", item.lang);
        await expect(page.getByRole("heading", { level: 1, name: item.title })).toHaveCount(1);
        await expect(page.locator("h1")).toHaveCount(1);
        await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
            "href", "https://safetech.ge" + path,
        );
        await expect(page.getByRole("link", { name: item.plannerLink })).toHaveAttribute(
            "href", item.prefix + "/camera-planner",
        );
        await expect(page.locator('meta[name="robots"]')).not.toHaveAttribute("content", /noindex/);
        expect((await page.locator("article").innerText()).length).toBeGreaterThan(1500);
    });

    test(item.lang + " services hub links to CCTV guide", async ({ page }) => {
        const response = await page.goto(item.prefix + "/services", { waitUntil: "domcontentloaded" });
        expect(response?.status()).toBe(200);
        await expect(page.getByRole("link", { name: item.guideLink })).toHaveAttribute(
            "href", item.prefix + "/guides/how-many-cameras-for-a-house",
        );
    });
}

test("main XML sitemap includes all three localized camera-count guide URLs", async ({ request }) => {
    const response = await request.get("/sitemap-main.xml");
    expect(response.status()).toBe(200);
    const xml = await response.text();
    for (const prefix of ["", "/en", "/ru"]) {
        expect(xml).toContain("https://safetech.ge" + prefix + "/guides/how-many-cameras-for-a-house");
    }
});
