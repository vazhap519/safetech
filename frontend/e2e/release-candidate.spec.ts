import { expect, test, type Locator, type Page } from "@playwright/test";

const apiBase = process.env.E2E_API_BASE || "http://127.0.0.1:8000/api";

type Locale = "ka" | "en" | "ru";

const locales: Array<{ locale: Locale; prefix: string; lang: string }> = [
    { locale: "ka", prefix: "", lang: "ka-GE" },
    { locale: "en", prefix: "/en", lang: "en-GE" },
    { locale: "ru", prefix: "/ru", lang: "ru-GE" },
];

const publicRoutes = ["/", "/about", "/services", "/projects", "/contact", "/privacy"];
const sitemapRoutes = [
    "/sitemap.xml",
    "/sitemap-main.xml",
    "/sitemap-services.xml",
    "/sitemap-local-services.xml",
    "/sitemap-service-categories.xml",
    "/sitemap-projects.xml",
    "/sitemap-project-categories.xml",
    "/sitemap-pages.xml",
    "/sitemap-images.xml",
    "/sitemap-blog.xml",
];

function localizedPath(prefix: string, path: string) {
    if (path === "/") return prefix || "/";
    return `${prefix}${path}`;
}

async function revealConsultationTriggers(page: Page): Promise<Locator> {
    const triggers = page.locator("[data-consultation-trigger]:visible");

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

async function revealMobileNavigation(page: Page) {
    const mobileMenu = page.locator("nav details:visible").first();

    if ((await mobileMenu.count()) === 0) return;
    if ((await mobileMenu.getAttribute("open")) !== null) return;

    const summary = mobileMenu.locator("summary");
    await expect(summary).toBeVisible();
    await summary.click();
    await expect(mobileMenu).toHaveAttribute("open", "");
}

async function openConsultation(page: Page) {
    const triggers = await revealConsultationTriggers(page);
    await triggers.first().click();

    const dialog = page.locator("#consultation-modal");
    await expect(dialog).toBeVisible();

    return dialog;
}

async function closeConsultation(page: Page) {
    const dialog = page.locator("#consultation-modal");

    if (await dialog.isVisible()) {
        await page.keyboard.press("Escape");
        await expect(dialog).toBeHidden();
    }
}

async function fillConsultationForm(form: Locator, phone: string, details: string) {
    const serviceSelect = form.locator('select[name="serviceSlug"]');
    await expect(serviceSelect).toBeEnabled();
    const serviceOptions = serviceSelect.locator("option:not([disabled])");
    expect(await serviceOptions.count()).toBeGreaterThan(0);
    const serviceValues = await serviceOptions.evaluateAll((options) =>
        options.map((option) => (option as HTMLOptionElement).value).filter(Boolean),
    );

    await serviceSelect.selectOption(serviceValues[0]);
    await form.locator('input[name="firstName"]').fill("QA");
    await form.locator('input[name="lastName"]').fill("Customer");
    await form.locator('input[name="phone"]').fill(phone);
    await form.locator('input[name="email"]').fill("qa-consultation@safetech.test");
    await form.locator('input[name="address"]').fill("Tbilisi");
    await form.locator('textarea[name="message"]').fill(details);
    await form.locator('input[name="privacy"]').check();
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

        test(`${locale} service local landing project category CMS page and review routes render`, async ({ page, request }) => {
            const servicesResponse = await request.get(`${apiBase}/services?locale=${locale}`);
            expect(servicesResponse.ok()).toBeTruthy();
            const services = (await servicesResponse.json()) as { data?: Array<{ slug?: string }> };
            const serviceSlug = services.data?.find((item) => item.slug)?.slug;
            expect(serviceSlug).toBeTruthy();

            const serviceResponse = await page.goto(localizedPath(prefix, `/services/${serviceSlug}`), {
                waitUntil: "domcontentloaded",
            });
            expect(serviceResponse?.status()).toBe(200);
            await expect(page.locator("h1").first()).toBeVisible();

            const localLandingsResponse = await request.get(
                `${apiBase}/local-service-landings?locale=${locale}`,
            );
            expect(localLandingsResponse.ok()).toBeTruthy();
            const localLandings = (await localLandingsResponse.json()) as {
                data?: Array<{
                    locationSlug?: string;
                    service?: { slug?: string };
                }>;
            };
            const localLanding = localLandings.data?.find(
                (item) => item.locationSlug && item.service?.slug,
            );
            expect(localLanding?.locationSlug).toBeTruthy();
            expect(localLanding?.service?.slug).toBeTruthy();

            const localLandingResponse = await page.goto(
                localizedPath(
                    prefix,
                    `/services/${localLanding!.service!.slug}/${localLanding!.locationSlug}`,
                ),
                { waitUntil: "domcontentloaded" },
            );
            expect(localLandingResponse?.status()).toBe(200);
            await expect(page.locator("h1").first()).toBeVisible();
            const localJsonLd = (
                await page.locator('script[type="application/ld+json"]').allTextContents()
            ).join(" ");
            expect(localJsonLd).toMatch(/"@type"\s*:\s*"Service"/);
            expect(localJsonLd).toMatch(/"@type"\s*:\s*"BreadcrumbList"/);

            const categoriesResponse = await request.get(`${apiBase}/service-categories?locale=${locale}`);
            expect(categoriesResponse.ok()).toBeTruthy();
            const categories = (await categoriesResponse.json()) as { data?: Array<{ slug?: string }> };
            const serviceCategorySlug = categories.data?.find((item) => item.slug)?.slug;
            expect(serviceCategorySlug).toBeTruthy();

            const serviceCategoryResponse = await page.goto(
                localizedPath(prefix, `/services/category/${serviceCategorySlug}`),
                { waitUntil: "domcontentloaded" },
            );
            expect(serviceCategoryResponse?.status()).toBe(200);

            const projectResponse = await page.goto(localizedPath(prefix, "/projects/qa-release-project"), {
                waitUntil: "domcontentloaded",
            });
            expect(projectResponse?.status()).toBe(200);
            await expect(page.locator("h1").first()).toBeVisible();

            const projectCategoryResponse = await page.goto(
                localizedPath(prefix, "/projects/category/qa-project-category"),
                { waitUntil: "domcontentloaded" },
            );
            expect(projectCategoryResponse?.status()).toBe(200);

            const cmsPageResponse = await page.goto(localizedPath(prefix, "/pages/qa-dynamic-page"), {
                waitUntil: "domcontentloaded",
            });
            expect(cmsPageResponse?.status()).toBe(200);
            await expect(page.locator("h1").first()).toContainText("QA Dynamic Page");

            const reviewResponse = await page.goto(localizedPath(prefix, "/review/qa-review-route"), {
                waitUntil: "domcontentloaded",
            });
            expect(reviewResponse?.status()).toBe(200);
            await expect(page.locator("form")).toBeVisible();
        });

        test(`${locale} every consultation CTA opens the same working form`, async ({ page }) => {
            for (const route of ["/", "/about", "/services", "/projects", "/contact"]) {
                await page.goto(localizedPath(prefix, route), { waitUntil: "domcontentloaded" });

                const triggers = await revealConsultationTriggers(page);
                const count = await triggers.count();
                expect(count).toBeGreaterThan(0);

                for (let index = 0; index < count; index += 1) {
                    await triggers.nth(index).click();
                    const dialog = page.locator("#consultation-modal");
                    await expect(dialog).toBeVisible();
                    await expect(dialog.locator('select[name="serviceSlug"]')).toBeEnabled();
                    await closeConsultation(page);
                }
            }
        });

        test(`${locale} internal navigation and CTA links avoid broken destinations`, async ({ page, request, baseURL }) => {
            expect(baseURL).toBeTruthy();
            const origin = new URL(baseURL!).origin;
            const internalUrls = new Set<string>();

            for (const route of publicRoutes) {
                await page.goto(localizedPath(prefix, route), { waitUntil: "domcontentloaded" });
                const hrefs = await page.locator('a[href]').evaluateAll((links) =>
                    links.map((link) => (link as HTMLAnchorElement).href),
                );

                for (const href of hrefs) {
                    expect(href).not.toMatch(/^javascript:/i);
                    const parsed = new URL(href);
                    if (parsed.origin === origin && !parsed.pathname.startsWith("/api/")) {
                        internalUrls.add(`${parsed.origin}${parsed.pathname}${parsed.search}`);
                    } else if (["tel:", "mailto:"].includes(parsed.protocol)) {
                        expect(parsed.pathname.trim()).not.toBe("");
                    }
                }
            }

            expect(internalUrls.size).toBeGreaterThan(5);
            for (const url of internalUrls) {
                const response = await request.get(url);
                expect(response.status(), `Broken internal destination: ${url}`).toBeLessThan(400);
            }
        });

        test(`${locale} consultation form submits through the full stack`, async ({ page }) => {
            await page.goto(localizedPath(prefix, "/services"), { waitUntil: "domcontentloaded" });

            const dialog = await openConsultation(page);
            const form = dialog.locator("form");
            await fillConsultationForm(
                form,
                "+995555000111",
                "Automated release candidate consultation submission test.",
            );

            const submission = page.waitForResponse(
                (response) => response.url().includes("/api/contact-leads") && response.request().method() === "POST",
            );
            await form.locator('button[type="submit"]').click();
            const response = await submission;

            expect(response.status()).toBe(201);
            await expect(form.locator('[role="status"]')).not.toBeEmpty();
        });

        test(`${locale} service calculator is interactive`, async ({ page }) => {
            await page.goto(localizedPath(prefix, "/services#service-calculator"), { waitUntil: "domcontentloaded" });

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
            const response = await page.goto(localizedPath(prefix, "/qa-this-route-must-not-exist"), {
                waitUntil: "domcontentloaded",
            });

            expect(response?.status()).toBe(404);
            await expect(page.locator("main")).toHaveCount(1);
            await expect(page.locator("body")).toContainText(/404|ვერ მოიძებნა|not found|не найдена/i);
        });
    }

    test("language switcher changes route and document locale", async ({ page }) => {
        await page.goto("/services", { waitUntil: "domcontentloaded" });

        await revealMobileNavigation(page);
        const english = page.locator('a[href="/en/services"]:visible').first();
        await expect(english).toBeVisible();
        await english.click();
        await expect(page).toHaveURL(/\/en\/services$/);
        await expect(page.locator("html")).toHaveAttribute("lang", "en-GE");

        await revealMobileNavigation(page);
        const russian = page.locator('a[href="/ru/services"]:visible').first();
        await expect(russian).toBeVisible();
        await russian.click();
        await expect(page).toHaveURL(/\/ru\/services$/);
        await expect(page.locator("html")).toHaveAttribute("lang", "ru-GE");
    });

    test("legacy service calculator route redirects to the services calculator", async ({ page }) => {
        await page.goto("/service-calculator", { waitUntil: "domcontentloaded" });
        await expect(page).toHaveURL(/\/services#service-calculator$/);
        await expect(page.locator("#service-calculator")).toBeVisible();
    });

    test("robots manifest and all sitemap routes are valid", async ({ request, baseURL }) => {
        expect(baseURL).toBeTruthy();

        const robots = await request.get(`${baseURL}/robots.txt`);
        expect(robots.status()).toBe(200);
        const robotsText = await robots.text();
        expect(robotsText).toContain("Disallow: /admin");
        expect(robotsText).toContain("Disallow: /api");
        expect(robotsText).toContain("sitemap.xml");

        for (const route of sitemapRoutes) {
            const response = await request.get(`${baseURL}${route}`);
            expect(response.status(), `Sitemap failed: ${route}`).toBe(200);
            const body = await response.text();
            expect(body).toMatch(/<(urlset|sitemapindex)[\s>]/);
        }

        const manifest = await request.get(`${baseURL}/manifest.webmanifest`);
        expect(manifest.status()).toBe(200);
        const manifestData = (await manifest.json()) as { name?: string; start_url?: string };
        expect(manifestData.name).toBeTruthy();
        expect(manifestData.start_url).toBe("/");
    });

    test("locale and revalidation API routes enforce expected behavior", async ({ request, baseURL }) => {
        const localeResponse = await request.post(`${baseURL}/api/locale`, {
            data: { locale: "en" },
        });
        expect(localeResponse.status()).toBe(200);
        expect((await localeResponse.json()).locale).toBe("en");
        expect(localeResponse.headers()["set-cookie"]).toContain("safetech_locale=en");

        const unauthorizedRevalidate = await request.post(`${baseURL}/api/revalidate`, {
            data: { tag: "cms" },
        });
        expect(unauthorizedRevalidate.status()).toBe(401);
    });

    test("runtime 500 renders the branded error boundary", async ({ page }) => {
        const response = await page.goto("/qa-runtime-error", { waitUntil: "domcontentloaded" });

        expect(response?.status()).toBe(500);
        await expect(page.locator("main")).toHaveCount(1);
        await expect(page.locator("body")).toContainText(/SafeTech/);
        await expect(page.locator("body")).toContainText(/ჩატვირთვა ვერ მოხერხდა|could not load|не удалось загрузить/i);
        await expect(page.locator("body")).not.toContainText(/Intentional release-candidate runtime error probe/i);
    });

    test("review invitation can be submitted once", async ({ page }, testInfo) => {
        const token = testInfo.project.name === "mobile" ? "qa-review-mobile" : "qa-review-desktop";
        const response = await page.goto(`/review/${token}`, { waitUntil: "domcontentloaded" });

        expect(response?.status()).toBe(200);

        const form = page.locator("form");
        await form.locator('input[name="author"]').fill("QA Reviewer");
        await form.locator('textarea[name="quote"]').fill("Automated release candidate review submission.");
        await form.locator('input[name="consent"]').check();

        const submission = page.waitForResponse(
            (item) => item.url().includes(`/api/review-invitations/${token}/submit`) && item.request().method() === "POST",
        );
        await form.locator('button[type="submit"]').click();
        const submitResponse = await submission;
        expect(submitResponse.status()).toBe(201);

        const secondAttempt = await page.request.post(`/api/review-invitations/${token}/submit`, {
            data: {
                author: "QA Reviewer",
                quote: "Second attempt must be rejected.",
                consent: true,
            },
        });
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
        await fillConsultationForm(form, "+995555000222", "Simulated gateway failure test.");
        await form.locator('button[type="submit"]').click();

        const status = form.locator('[role="status"]');
        await expect(status).not.toBeEmpty();
        await expect(status).not.toContainText(/json|unexpected token|failed to fetch|aborterror/i);
    });
});
