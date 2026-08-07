import { expect, test, type Locator, type Page } from "@playwright/test";

const apiBase = process.env.E2E_API_BASE || "http://127.0.0.1:8000/api";

type Locale = "ka" | "en" | "ru";

const locales: Array<{ locale: Locale; prefix: string; lang: string }> = [
    { locale: "ka", prefix: "", lang: "ka-GE" },
    { locale: "en", prefix: "/en", lang: "en-GE" },
    { locale: "ru", prefix: "/ru", lang: "ru-GE" },
];

const publicRoutes = ["/", "/about", "/services", "/projects", "/contact", "/privacy"];

function localizedPath(prefix: string, path: string) {
    if (path === "/") return prefix || "/";
    return `${prefix}${path}`;
}

async function revealConsultationTriggers(page: Page): Promise<Locator> {
    const triggers = page.locator(
        '[popovertarget="consultation-popover"]:visible',
    );

    if ((await triggers.count()) === 0) {
        const mobileMenu = page.locator("nav details:visible").first();
        const summary = mobileMenu.locator("summary");

        await expect(summary).toBeVisible();
        await summary.click();
        await expect(mobileMenu).toHaveAttribute("open", "");
    }

    await expect(triggers.first()).toBeVisible();

    return triggers;
}

async function openConsultation(page: Page) {
    const triggers = await revealConsultationTriggers(page);
    await triggers.first().click();

    const dialog = page.locator("#consultation-popover");
    await expect(dialog).toBeVisible();

    return dialog;
}

async function closeConsultation(page: Page) {
    const dialog = page.locator("#consultation-popover");
    const close = dialog.locator('[popovertargetaction="hide"]').first();

    if (await close.isVisible()) {
        await close.click();
        await expect(dialog).toBeHidden();
    }
}

test.describe("release candidate public matrix", () => {
    for (const { locale, prefix, lang } of locales) {
        for (const route of publicRoutes) {
            test(`${locale} ${route} renders with one main landmark`, async ({ page }) => {
                const response = await page.goto(localizedPath(prefix, route), {
                    waitUntil: "domcontentloaded",
                });

                expect(response?.status()).toBe(200);
                await expect(page.locator("html")).toHaveAttribute("lang", lang);
                await expect(page.locator("main")).toHaveCount(1);
                await expect(page.locator("body")).not.toBeEmpty();
            });
        }

        test(`${locale} dynamic service and project routes render`, async ({ page, request }) => {
            const servicesResponse = await request.get(`${apiBase}/services?locale=${locale}`);
            expect(servicesResponse.ok()).toBeTruthy();
            const services = (await servicesResponse.json()) as {
                data?: Array<{ slug?: string }>;
            };
            const serviceSlug = services.data?.find((item) => item.slug)?.slug;
            expect(serviceSlug).toBeTruthy();

            const serviceResponse = await page.goto(
                localizedPath(prefix, `/services/${serviceSlug}`),
                { waitUntil: "domcontentloaded" },
            );
            expect(serviceResponse?.status()).toBe(200);
            await expect(page.locator("h1").first()).toBeVisible();

            const projectsResponse = await request.get(`${apiBase}/projects?locale=${locale}`);
            expect(projectsResponse.ok()).toBeTruthy();
            const projects = (await projectsResponse.json()) as {
                data?: Array<{ slug?: string }>;
            };
            const projectSlug = projects.data?.find((item) => item.slug)?.slug;

            if (projectSlug) {
                const projectResponse = await page.goto(
                    localizedPath(prefix, `/projects/${projectSlug}`),
                    { waitUntil: "domcontentloaded" },
                );
                expect(projectResponse?.status()).toBe(200);
                await expect(page.locator("h1").first()).toBeVisible();
            }
        });

        test(`${locale} every visible consultation CTA opens the same working form`, async ({ page }) => {
            for (const route of ["/", "/about", "/services", "/projects", "/contact"]) {
                await page.goto(localizedPath(prefix, route), {
                    waitUntil: "domcontentloaded",
                });

                const triggers = await revealConsultationTriggers(page);
                const count = await triggers.count();
                expect(count).toBeGreaterThan(0);

                for (let index = 0; index < count; index += 1) {
                    await triggers.nth(index).click();
                    const dialog = page.locator("#consultation-popover");
                    await expect(dialog).toBeVisible();
                    await expect(dialog.locator('select[name="serviceSlug"]')).toBeEnabled();
                    await closeConsultation(page);
                }
            }
        });

        test(`${locale} consultation form submits through the full stack`, async ({ page }) => {
            await page.goto(localizedPath(prefix, "/services"), {
                waitUntil: "domcontentloaded",
            });

            const dialog = await openConsultation(page);
            const form = dialog.locator("form");
            const serviceSelect = form.locator('select[name="serviceSlug"]');
            await expect(serviceSelect).toBeEnabled();
            const serviceOptions = serviceSelect.locator("option:not([disabled])");
            expect(await serviceOptions.count()).toBeGreaterThan(0);
            const serviceValues = await serviceOptions.evaluateAll((options) =>
                options
                    .map((option) => (option as HTMLOptionElement).value)
                    .filter(Boolean),
            );

            expect(serviceValues.length).toBeGreaterThan(0);
            await serviceSelect.selectOption(serviceValues[0]);
            await form.locator('input[name="firstName"]').fill("QA");
            await form.locator('input[name="lastName"]').fill("Release Candidate");
            await form.locator('input[name="phone"]').fill("+995555000111");
            await form
                .locator('input[name="email"]')
                .fill(`qa-${locale}-${test.info().project.name}@safetech.test`);
            await form.locator('input[name="address"]').fill("Tbilisi QA");
            await form
                .locator('textarea[name="details"]')
                .fill("Automated release candidate consultation submission test.");
            await form.locator('input[name="privacy"]').check();

            const submission = page.waitForResponse(
                (response) =>
                    response.url().includes("/api/contact-leads") &&
                    response.request().method() === "POST",
            );
            await form.locator('button[type="submit"]').click();
            const response = await submission;

            expect(response.status()).toBe(201);
            await expect(form.locator('[role="status"]')).not.toBeEmpty();
        });

        test(`${locale} service calculator is interactive`, async ({ page }) => {
            await page.goto(localizedPath(prefix, "/services#service-calculator"), {
                waitUntil: "domcontentloaded",
            });

            const calculator = page.locator("#service-calculator");
            await expect(calculator).toBeVisible();

            const serviceSelect = calculator.locator("select").first();
            const options = await serviceSelect.locator("option").count();
            expect(options).toBeGreaterThan(0);

            if (options > 1) {
                const secondValue = await serviceSelect.locator("option").nth(1).getAttribute("value");
                expect(secondValue).toBeTruthy();
                await serviceSelect.selectOption(secondValue!);
                await expect(serviceSelect).toHaveValue(secondValue!);
            }

            const numberInput = calculator.locator('input[type="number"]').first();
            if (await numberInput.count()) {
                const current = Number((await numberInput.inputValue()) || "0");
                const min = Number((await numberInput.getAttribute("min")) || "0");
                const maxAttribute = await numberInput.getAttribute("max");
                const max = maxAttribute ? Number(maxAttribute) : current + 2;
                const next = Math.min(max, Math.max(min, current + 1));
                await numberInput.fill(String(next));
                await expect(numberInput).toHaveValue(String(next));
            }

            await expect(calculator).toContainText(/\d/);
        });

        test(`${locale} unknown route returns branded 404`, async ({ page }) => {
            const response = await page.goto(
                localizedPath(prefix, "/qa-this-route-must-not-exist"),
                { waitUntil: "domcontentloaded" },
            );

            expect(response?.status()).toBe(404);
            await expect(page.locator("main")).toHaveCount(1);
            await expect(page.locator("body")).toContainText(/404|ვერ მოიძებნა|not found|не найдена/i);
        });
    }

    test("robots and sitemap expose expected production crawl rules", async ({ request, baseURL }) => {
        expect(baseURL).toBeTruthy();

        const robots = await request.get(`${baseURL}/robots.txt`);
        expect(robots.status()).toBe(200);
        const robotsText = await robots.text();
        expect(robotsText).toContain("Disallow: /admin");
        expect(robotsText).toContain("Disallow: /api");
        expect(robotsText).toContain("sitemap.xml");

        const sitemapIndex = await request.get(`${baseURL}/sitemap.xml`);
        expect(sitemapIndex.status()).toBe(200);
        const sitemapIndexText = await sitemapIndex.text();
        expect(sitemapIndexText).toContain("<sitemapindex");
        expect(sitemapIndexText).toContain("/sitemap-main.xml");
        expect(sitemapIndexText).toContain("/sitemap-services.xml");
        expect(sitemapIndexText).toContain("/sitemap-projects.xml");

        const mainSitemap = await request.get(`${baseURL}/sitemap-main.xml`);
        expect(mainSitemap.status()).toBe(200);
        const mainSitemapText = await mainSitemap.text();
        expect(mainSitemapText).toContain("<urlset");
        expect(mainSitemapText).toContain("/en");
        expect(mainSitemapText).toContain("/ru");
        expect(mainSitemapText).toContain("/services");
    });

    test("review invitation can be submitted once", async ({ page }, testInfo) => {
        const token = testInfo.project.name === "mobile" ? "qa-review-mobile" : "qa-review-desktop";
        const response = await page.goto(`/review/${token}`, {
            waitUntil: "domcontentloaded",
        });

        expect(response?.status()).toBe(200);

        const form = page.locator("form");
        await form.locator('input[name="author"]').fill("QA Reviewer");
        await form.locator('textarea[name="quote"]').fill(
            "Automated release candidate review submission.",
        );
        await form.locator('input[name="consent"]').check();

        const submission = page.waitForResponse(
            (item) =>
                item.url().includes(`/api/review-invitations/${token}/submit`) &&
                item.request().method() === "POST",
        );
        await form.locator('button[type="submit"]').click();
        const submitResponse = await submission;
        expect(submitResponse.status()).toBe(201);

        const secondAttempt = await page.request.post(
            `/api/review-invitations/${token}/submit`,
            {
                data: {
                    author: "QA Reviewer",
                    quote: "Second attempt must be rejected.",
                    consent: true,
                },
            },
        );
        expect(secondAttempt.status()).toBe(422);
    });

    test("consultation shows a safe message for a simulated gateway failure", async ({ page }) => {
        await page.route("**/api/contact-leads", async (route) => {
            await route.fulfill({
                status: 502,
                contentType: "text/html",
                body: "<html><body>Bad Gateway</body></html>",
            });
        });

        await page.goto("/services", { waitUntil: "domcontentloaded" });
        const dialog = await openConsultation(page);
        const form = dialog.locator("form");
        const serviceSelect = form.locator('select[name="serviceSlug"]');
        await expect(serviceSelect).toBeEnabled();
        const values = await serviceSelect
            .locator("option:not([disabled])")
            .evaluateAll((options) =>
                options.map((option) => (option as HTMLOptionElement).value).filter(Boolean),
            );
        expect(values.length).toBeGreaterThan(0);
        await serviceSelect.selectOption(values[0]);
        await form.locator('input[name="firstName"]').fill("QA");
        await form.locator('input[name="lastName"]').fill("Failure Test");
        await form.locator('input[name="phone"]').fill("+995555000222");
        await form.locator('input[name="email"]').fill("qa-failure@safetech.test");
        await form.locator('input[name="address"]').fill("Tbilisi QA");
        await form.locator('textarea[name="details"]').fill("Simulated gateway failure test.");
        await form.locator('input[name="privacy"]').check();
        await form.locator('button[type="submit"]').click();

        const status = form.locator('[role="status"]');
        await expect(status).not.toBeEmpty();
        await expect(status).not.toContainText(/json|unexpected token|failed to fetch|aborterror/i);
    });
});
