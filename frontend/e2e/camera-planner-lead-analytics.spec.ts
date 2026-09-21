import { expect, test } from "@playwright/test";

type CapturedEvent = {
    event_type: string;
    service_slug?: string | null;
    page_path?: string | null;
    meta?: Record<string, unknown>;
};

const cases = [
    { consent: "accepted", submissionStatus: 201, leadEvents: 1 },
    { consent: "rejected", submissionStatus: 201, leadEvents: 0 },
    { consent: "accepted", submissionStatus: 422, leadEvents: 0 },
] as const;

for (const item of cases) {
    test(`planner consent=${item.consent} submit=${item.submissionStatus} tracks verified leads only`, async ({ page }) => {
        const baseURL = process.env.E2E_BASE_URL || "http://127.0.0.1:3000";
        await page.context().addCookies([{
            name: "safetech_marketing_consent",
            value: item.consent,
            url: baseURL,
        }]);
        await page.addInitScript((consent) => {
            localStorage.setItem("safetech_marketing_consent", consent);
        }, item.consent);

        const captured: CapturedEvent[] = [];
        await page.route("**/api/analytics/events", async (route) => {
            const payload = route.request().postDataJSON() as CapturedEvent;
            captured.push(payload);
            await route.fulfill({
                status: 201,
                contentType: "application/json",
                body: JSON.stringify({ data: { tracked: true, eventType: payload.event_type } }),
            });
        });
        await page.route("**/api/camera-plans", async (route) => {
            expect(route.request().method()).toBe("POST");
            await route.fulfill({
                status: item.submissionStatus,
                contentType: "application/json",
                body: JSON.stringify(item.submissionStatus === 201
                    ? { message: "Accepted", data: { id: 1 } }
                    : { message: "Validation failed", errors: { layout: ["Invalid"] } }),
            });
        });

        await page.goto("/camera-planner", { waitUntil: "domcontentloaded" });
        const canvas = page.locator("canvas[role='img']");
        await expect(canvas).toBeVisible();
        await page.getByRole("button", { name: "კამერის დამატება" }).click();
        await canvas.click({ position: { x: 90, y: 80 } });

        const form = page.locator("aside form");
        await form.locator("input").nth(0).fill("Test property");
        await form.locator("input").nth(1).fill("Test applicant");
        await form.locator("input").nth(2).fill("+995555000111");
        await form.locator('input[type="checkbox"]').check();

        const accepted = page.waitForResponse((res) =>
            res.url().endsWith("/api/camera-plans")
            && res.request().method() === "POST",
        );
        await form.locator('button[type="submit"]').click();
        expect((await accepted).status()).toBe(item.submissionStatus);
        await expect(form.locator('[role="status"]')).not.toBeEmpty();

        if (item.leadEvents) {
            await expect.poll(() => captured.filter((event) => event.event_type === "lead_created").length)
                .toBe(item.leadEvents);
            const event = captured.find((value) => value.event_type === "lead_created");
            expect(event).toMatchObject({
                event_type: "lead_created",
                page_path: "/camera-planner",
                service_slug: "security-camera-installation",
                meta: { source: "camera-planner" },
            });
        } else {
            // Fetch and sendBeacon are both asynchronous. Give unwanted events
            // time to arrive rather than asserting synchronously.
            await page.waitForTimeout(350);
            expect(captured.filter((event) => event.event_type === "lead_created")).toHaveLength(0);
        }

        for (const event of captured) {
            const serialized = JSON.stringify(event);
            expect(serialized).not.toContain("+995555000111");
            expect(serialized).not.toContain("Test applicant");
            expect(serialized).not.toContain("Test property");
        }
    });
}
