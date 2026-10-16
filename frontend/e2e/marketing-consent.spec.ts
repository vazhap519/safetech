import { expect, test, type Page } from "@playwright/test";

async function googleEvents(page: Page, name: string) {
    return page.evaluate((eventName) => (window.dataLayer || [])
        .map((item) => Array.from(item as ArrayLike<unknown>))
        .filter((item) => item[0] === "event" && item[1] === eventName), name);
}

async function metaPageViews(page: Page) {
    return page.evaluate(() => {
        const fbq = window.fbq as unknown as { queue?: unknown[][] };
        return (fbq?.queue || []).filter((item) => item[0] === "track" && item[1] === "PageView").length;
    });
}

test.beforeEach(async ({ page }) => {
    // No test traffic reaches Google, Meta, TOP.GE or production lead endpoints.
    await page.route(/https:\/\/(www\.googletagmanager\.com|connect\.facebook\.net|counter\.top\.ge)\//,
        (route) => route.request().url().includes("count222")
            ? route.fulfill({ contentType: "image/png", body: Buffer.from("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIHWP4z8DwHwAFgAI/ScLbtAAAAABJRU5ErkJggg==", "base64") })
            : route.fulfill({ contentType: "application/javascript", body: "/* test transport */" }));
    await page.route("**/api/analytics/events", (route) => route.fulfill({ json: { data: { tracked: true } } }));
});

test("consent gates tags; GA4 owns initial and SPA views; revocation stops app events", async ({ page }) => {
    const loaded: string[] = [];
    const counterRequests: string[] = [];
    page.on("request", (request) => {
        if (/googletagmanager|fbevents\.js/.test(request.url())) loaded.push(request.url());
        if (request.url().includes("counter.top.ge/cgi-bin/count222")) counterRequests.push(request.url());
    });
    const errors: string[] = [];
    page.on("pageerror", (error) => errors.push(error.message));
    await page.goto("/qa-marketing/one?mode=both");
    await expect(page.getByRole("button", { name: "დათანხმება", exact: true })).toBeVisible();
    expect(loaded).toHaveLength(0);
    expect(counterRequests).toHaveLength(0);
    await expect(page.locator("[data-qa-counter]")).toHaveCount(0);

    await page.getByRole("button", { name: "დათანხმება", exact: true }).click();
    await expect.poll(() => googleEvents(page, "page_view")).toHaveLength(1);
    await expect.poll(() => metaPageViews(page)).toBe(1);
    await expect(page.locator("[data-qa-counter]")).toHaveCount(1);
    await expect.poll(() => counterRequests.length).toBe(1);
    expect(loaded.filter((url) => url.includes("gtm.js"))).toHaveLength(0);
    expect(loaded.filter((url) => url.includes("gtag/js"))).toHaveLength(1);
    expect(await page.evaluate(() => (window.dataLayer || []).map((x) => Array.from(x as ArrayLike<unknown>))
        .filter((x) => x[0] === "config").every((x) => (x[2] as { send_page_view?: boolean }).send_page_view === false))).toBe(true);

    for (const [label, event] of [["QA phone", "phone_click"], ["QA email", "email_click"], ["QA WhatsApp", "whatsapp_click"]]) {
        await page.getByRole("link", { name: label, exact: true }).click();
        await expect.poll(() => googleEvents(page, event)).toHaveLength(1);
    }
    await page.getByRole("link", { name: "Next test view" }).click();
    await expect.poll(() => googleEvents(page, "page_view")).toHaveLength(2);
    await expect.poll(() => metaPageViews(page)).toBe(2);
    await expect.poll(() => counterRequests.length).toBe(2);
    const second = (await googleEvents(page, "page_view"))[1][2] as Record<string, string>;
    expect(second.page_referrer).toContain("/qa-marketing/one");
    expect(second.page_path).toBe("/qa-marketing/two");
    await page.getByRole("button", { name: "კონფიდენციალურობის პარამეტრები", exact: true }).click();
    await page.getByRole("button", { name: "უარყოფა", exact: true }).click();
    await page.getByRole("link", { name: "QA phone", exact: true }).click();
    expect(await googleEvents(page, "phone_click")).toHaveLength(1);
    await expect(page.locator("[data-qa-counter]")).toHaveCount(0);
    expect(await page.evaluate(() => (window as unknown as Record<string, unknown>)["ga-disable-G-QATEST1"])).toBe(true);
    await page.getByRole("link", { name: "Next test view" }).click();
    await expect(page).toHaveURL(/qa-marketing\/one/);
    expect(counterRequests).toHaveLength(2);
    expect(await googleEvents(page, "page_view")).toHaveLength(2);
    expect(errors).toEqual([]);
});

for (const mode of ["gtm", "meta"]) {
    test(`${mode}-only setup records the first and next view`, async ({ page }) => {
        await page.goto(`/qa-marketing/one?mode=${mode}`);
        await page.getByRole("button", { name: "დათანხმება", exact: true }).click();
        await expect.poll(() => metaPageViews(page)).toBe(1);
        await page.getByRole("link", { name: "Next test view" }).click();
        await expect.poll(() => metaPageViews(page)).toBe(2);
        if (mode === "gtm") {
            expect(await page.evaluate(() => (window.dataLayer || []).filter((x) =>
                (x as { event?: string }).event === "virtual_page_view").length)).toBe(2);
        }
    });
}

test("revoking consent in another tab also disables the current tab", async ({ page }) => {
    await page.goto("/qa-marketing/one?mode=both");
    await page.getByRole("button", { name: "დათანხმება", exact: true }).click();
    await expect.poll(() => googleEvents(page, "page_view")).toHaveLength(1);
    await page.evaluate(() => {
        localStorage.setItem("safetech_marketing_consent", "rejected");
        document.cookie = "safetech_marketing_consent=rejected; Path=/";
        window.dispatchEvent(new StorageEvent("storage", {
            key: "safetech_marketing_consent", newValue: "rejected",
        }));
    });
    await expect(page.locator("[data-qa-counter]")).toHaveCount(0);
    expect(await page.evaluate(() => (window as unknown as Record<string, unknown>)["ga-disable-G-QATEST1"])).toBe(true);
    await page.getByRole("link", { name: "QA phone", exact: true }).click();
    expect(await googleEvents(page, "phone_click")).toHaveLength(0);
});

for (const item of [
    { status: 201, replayed: false, consent: "accepted", expected: 1 },
    { status: 200, replayed: true, consent: "accepted", expected: 0 },
    { status: 422, replayed: false, consent: "accepted", expected: 0 },
    { status: 201, replayed: false, consent: "rejected", expected: 0 },
]) {
    test(`lead ${item.status} replay=${item.replayed} consent=${item.consent}`, async ({ page }) => {
        let requests = 0;
        await page.route("**/api/contact-leads", async (route) => {
            requests++;
            await route.fulfill({ status: item.status, json: {
                message: item.status === 422 ? "Validation failed" : "Accepted",
                data: { id: 123, replayed: item.replayed },
            } });
        });
        await page.goto("/qa-marketing/one?mode=both");
        await page.getByRole("button", { name: item.consent === "accepted" ? "დათანხმება" : "უარყოფა", exact: true }).click();
        if (item.consent === "accepted") await expect.poll(() => googleEvents(page, "page_view")).toHaveLength(1);
        const form = page.locator("[data-qa-lead]");
        await form.getByRole("button", { name: "Submit test enquiry" }).click();
        await expect(form.getByRole("status")).not.toBeEmpty();
        expect(requests).toBe(1);
        expect(await googleEvents(page, "generate_lead")).toHaveLength(item.expected);
        for (const event of await googleEvents(page, "generate_lead")) {
            expect(JSON.stringify(event)).not.toMatch(/995555000111|qa@safetech\.test|Test enquiry|Customer/);
        }
    });
}
