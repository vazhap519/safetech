import { expect, test } from "@playwright/test";
import { intercomValues } from "../src/lib/intercom-calculator";
import { initialCalculatorValues, getCompatibleComponents, calculateConfiguratorTotals, type CalculatorProfile } from "../src/lib/service-calculator";

const service = "intercom-access-control-installation";
const api = process.env.E2E_API_BASE || "http://127.0.0.1:8000/api";
const copy = {
    ka: { prefix: "", apartments: "ბინების / აბონენტების რაოდენობა", doors: "შესასვლელი კარების რაოდენობა", panel: "გარე პანელი", choose: "შესაბამისი მოწყობილობების შერჩევა", quote: "ფასი დასაზუსტებელია" },
    en: { prefix: "/en", apartments: "Apartments / subscribers", doors: "Entrance doors", panel: "Door station", choose: "Choose matching devices", quote: "Price on request" },
    ru: { prefix: "/ru", apartments: "Квартиры / абоненты", doors: "Входные двери", panel: "Вызывная панель", choose: "Подобрать устройства", quote: "Цена по запросу" },
};

for (const [locale, labels] of Object.entries(copy)) {
    test(`intercom staged selection and quantities (${locale})`, async ({ page }) => {
        const errors: string[] = [];
        page.on("pageerror", (error) => errors.push(error.message));
        await page.goto(`${labels.prefix}/services?service=${service}#service-calculator`);
        const scope = page.locator("#service-calculator");
        await expect(scope.getByLabel(labels.apartments)).toBeVisible();
        const rejectCookies = page.getByRole("button", { name: /^(უარყოფა|Reject|Отклонить)$/ });
        if (await rejectCookies.count()) await rejectCookies.click();
        await expect(scope.getByLabel(labels.apartments).locator("option")).toHaveCount(100);
        await expect(scope.getByLabel(labels.doors).locator("option")).toHaveCount(10);
        await expect(scope.getByRole("combobox", { name: labels.panel, exact: true })).toHaveCount(0);
        await scope.getByLabel(labels.apartments).selectOption("100");
        await scope.getByLabel(labels.doors).selectOption("10");
        await scope.getByRole("button", { name: labels.choose, exact: true }).click();
        await expect(scope.getByRole("combobox", { name: labels.panel, exact: true })).toHaveValue("tvt-td-e2223");
        await expect(scope.getByRole("combobox", { name: labels.panel, exact: true }).locator("option")).toHaveCount(1);
        const monitor = scope.locator('[data-component-key="tvt-td-e2137"]');
        await expect(monitor.locator('input[type="number"]')).toHaveValue("100");
        await expect(monitor.locator('input[type="number"]')).toBeDisabled();
        await expect(scope.locator('[data-component-key="lock-maglock"] input[type="number"]')).toHaveValue("10");
        await expect(scope.locator('[data-component-key="generic-standard-poe-24"] input[type="number"]')).toHaveValue("6");
        await expect(monitor.getByText(labels.quote, { exact: true })).toBeVisible();
        await expect(page.getByTestId("intercom-capacity")).toContainText("2032.8");
        if (locale === "en") {
            await scope.getByRole("button", { name: "Request an exact quote", exact: true }).click();
            await expect(page.locator('#consultation-modal textarea[name="message"]')).toHaveValue(/Price on request/);
            await page.keyboard.press("Escape");
            await expect(page.locator("#consultation-modal")).toBeHidden();
        }
        await scope.getByLabel(labels.apartments).selectOption("1");
        await scope.getByLabel(labels.doors).selectOption("1");
        await expect(monitor).toHaveCount(0);
        await scope.getByRole("button", { name: labels.choose, exact: true }).click();
        await expect(monitor.locator('input[type="number"]')).toHaveValue("1");
        await expect(scope.getByRole("combobox", { name: labels.panel, exact: true }).locator('option[value="tvt-td-e3110"]')).toHaveCount(1);
        await scope.getByRole("combobox", { name: labels.panel, exact: true }).selectOption("tvt-te-vd1108");
        await expect(scope.locator('[data-component-key="tvt-te-vh1104"] input[type="number"]')).toHaveValue("1");
        await expect(monitor).toHaveCount(0);
        await expect(page.locator("[data-nextjs-dialog]")).toHaveCount(0);
        expect(errors).toEqual([]);
        expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth + 1)).toBeTruthy();
        await scope.screenshot({ path: `test-results/intercom-${locale}-${test.info().project.name}.png` });
    });
}

test("catalog-driven calculator uses PoE power and required quantities", async ({ request }) => {
    const response = await request.get(`${api}/service-calculator/profiles?service=${service}&locale=en`);
    expect(response.ok()).toBeTruthy();
    const profile = (await response.json()).data[0] as CalculatorProfile;
    expect(profile.intercomCatalog).toBeTruthy();
    const values = intercomValues(profile.intercomCatalog!, { ...initialCalculatorValues(profile), apartments: 100, doors: 10, monitors_per_apartment: 6, switch_id: "generic-standard-poe-4", cards_per_apartment: 0, backup_power: false });
    expect(values.monitor_count).toBe(600);
    expect(values.switch_id).toBe("auto");
    expect(values.switch_count).toBe(31);
    expect(values.core_ports).toBe(48);
    expect(Number(values.poe_available_w)).toBeGreaterThanOrEqual(Number(values.poe_required_w));
    const items = getCompatibleComponents(profile, values, "ip", "apartment-building", "");
    expect(items.find((i) => i.component.key === "mifare-tags")).toBeUndefined();
    expect(items.find((i) => i.component.key === "network-ups")).toBeUndefined();
    expect(items.find((i) => i.component.key === "tvt-td-e2137")?.quantity).toBe(600);
    const priced = { ...profile, components: profile.components.map((c) => c.key === "tvt-td-e2137" ? { ...c, unitPrice: 10 } : { ...c, unitPrice: 0 }) };
    const selected = getCompatibleComponents(priced, values, "ip", "apartment-building", "");
    const result = calculateConfiguratorTotals({ ...priced, laborPrice: 0, discountPercentage: 0 }, { oneTime: 0, monthly: 0, lines: [] }, selected, { "tvt-td-e2137": { selected: false, quantity: 1 } });
    expect(result.total).toBe(6000);
});

test("staff intercom form supports the full building scope on mobile and desktop", async ({ page }) => {
    const backend = api.replace(/\/api\/?$/, "");
    await page.goto(`${backend}/admin/login`);
    await page.locator('input[type="email"]').fill("qa-admin@safetech.test");
    await page.locator('input[type="password"]').fill("SafeTechQaPass123!");
    await page.locator('button[type="submit"]').click();
    await page.waitForURL((url) => !url.pathname.endsWith("/login"));
    await page.goto(`${backend}/admin/access-intercom-configurator-page`);
    const apartments = page.getByRole("combobox", { name: "ბინების / აბონენტების რაოდენობა", exact: true });
    const doors = page.getByRole("combobox", { name: "შესასვლელი კარების რაოდენობა", exact: true });
    await expect(apartments.locator("option")).toHaveCount(100);
    await expect(doors.locator("option")).toHaveCount(10);
    await apartments.selectOption("100");
    await doors.selectOption("10");
    await page.getByRole("button", { name: "შესაბამისი მოწყობილობების შერჩევა", exact: true }).click();
    await expect(page.getByText("100 ბინა/აბონენტი · 10 კარი · 100 მონიტორი", { exact: false })).toBeVisible();
    await expect(page.getByRole("combobox", { name: "გარე პანელი", exact: true })).toHaveValue("tvt-td-e2223");
    expect(await page.evaluate(() => document.documentElement.scrollWidth <= window.innerWidth + 1)).toBeTruthy();
    await page.screenshot({ path: `test-results/intercom-admin-${test.info().project.name}.png`, fullPage: true });
    await apartments.selectOption("1");
    await doors.selectOption("1");
    await expect(page.getByRole("heading", { name: "სრული კომპლექტაცია", exact: true })).toBeHidden();
    await page.getByRole("button", { name: "შესაბამისი მოწყობილობების შერჩევა", exact: true }).click();
    await expect(page.getByText("1 ბინა/აბონენტი · 1 კარი · 1 მონიტორი", { exact: false })).toBeVisible();
});
