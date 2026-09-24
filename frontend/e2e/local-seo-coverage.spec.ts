import { expect, test } from "@playwright/test";

const apiBase = process.env.E2E_API_BASE || "http://127.0.0.1:8000/api";
const missingServices = [
    "operating-system-installation",
    "custom-computer-build",
    "computer-cleaning-maintenance",
    "rack-assembly-cable-management",
    "pos-system-installation",
    "patch-panel-network-outlet-installation",
];

const locales = [
    { code: "ka", prefix: "", lang: "ka-GE" },
    { code: "en", prefix: "/en", lang: "en-GE" },
    { code: "ru", prefix: "/ru", lang: "ru-GE" },
];

type Landing = {
    locationSlug: string;
    service: { slug: string };
    title: string;
    content: string;
    seo: { title: string; description: string; noindex: boolean };
};

test("every published canonical service has an indexable Local page in all 3 languages", async ({ request }) => {
    const serviceResponse = await request.get(apiBase + "/services?locale=ka");
    expect(serviceResponse.status()).toBe(200);
    const services = (await serviceResponse.json()).data as Array<{ slug: string }>;
    expect(services).toHaveLength(57);

    for (const { code } of locales) {
        const response = await request.get(apiBase + "/local-service-landings?locale=" + code);
        expect(response.status()).toBe(200);
        const landings = (await response.json()).data as Landing[];
        const covered = new Set(landings.filter((landing) => !landing.seo.noindex)
            .map((landing) => landing.service.slug));
        for (const service of services) {
            expect(covered.has(service.slug), code + ": missing Local SEO for " + service.slug).toBe(true);
        }
        for (const slug of missingServices) {
            const landing = landings.find((item) => item.service.slug === slug
                && item.locationSlug === "tbilisi");
            expect(landing, code + ": " + slug + "/tbilisi").toBeDefined();
            expect(landing?.title).toBeTruthy();
            expect(landing?.content.length).toBeGreaterThan(250);
            expect(landing?.seo.description).toBeTruthy();
            expect(landing?.seo.noindex).toBe(false);
            if (code !== "ka") {
                expect(landing?.content, code + ": Georgian fallback in " + slug).not.toMatch(/[\u10A0-\u10FF]/);
            }
        }
    }
});

for (const { code, prefix, lang } of locales) {
    test(code + " each new Local page has one H1, one Service schema and canonical", async ({ page, request }) => {
        for (const slug of missingServices) {
            const path = "/services/" + slug + "/tbilisi";
            const response = await page.goto(prefix + path, { waitUntil: "domcontentloaded" });
            expect(response?.status(), code + ": " + path).toBe(200);
            await expect(page.locator("html")).toHaveAttribute("lang", lang);
            await expect(page.locator("h1")).toHaveCount(1);
            const robots = await page.locator('meta[name="robots"]').getAttribute("content");
            expect(robots || "").not.toContain("noindex");
            const canonical = await page.locator('link[rel="canonical"]').getAttribute("href");
            expect(canonical).toContain(prefix + path);

            const scripts = await page.locator('script[type="application/ld+json"]').allTextContents();
            const types = scripts.flatMap((json) => {
                const parsed: unknown = JSON.parse(json);
                return Array.isArray(parsed) ? parsed : [parsed];
            }).filter((data): data is { "@type"?: string } => typeof data === "object" && data !== null);
            expect(types.filter((data) => data["@type"] === "Service")).toHaveLength(1);

            const api = await request.get(apiBase + "/local-service-landings/" + slug + "/tbilisi?locale=" + code);
            expect(api.status()).toBe(200);
            const landing = (await api.json()).data as Landing;
            const browserH1 = (await page.locator("h1").textContent())?.trim();
            expect(browserH1).toBe(landing.title);
            const description = await page.locator('meta[name="description"]').getAttribute("content");
            expect(description).toContain(landing.seo.description);
        }
    });
}

for (const { code, prefix } of locales) {
    test(code + " CCTV Tbilisi landing links to the localized camera planner", async ({ page }) => {
        const response = await page.goto(
            prefix + "/services/security-camera-installation/tbilisi",
            { waitUntil: "domcontentloaded" },
        );
        expect(response?.status()).toBe(200);
        const link = page.getByRole("link", {
            name: {
                ka: "კამერების განლაგების დაგეგმვა",
                en: "Plan your camera layout",
                ru: "Спланировать размещение камер",
            }[code as "ka" | "en" | "ru"],
        });
        await expect(link).toHaveAttribute("href", prefix + "/camera-planner");
    });
}

test("Local sitemap includes every additional indexable Tbilisi service", async ({ request }) => {
    const response = await request.get("/sitemap-local-services.xml");
    expect(response.status()).toBe(200);
    const body = await response.text();
    for (const slug of missingServices) {
        expect(body).toContain("/services/" + slug + "/tbilisi");
    }
});
