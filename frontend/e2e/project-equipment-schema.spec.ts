import { expect, test } from "@playwright/test";

const projectSlug = "kerdzo-sakhlis-susti-denebis-infrastruqtura";

for (const locale of ["", "/en", "/ru"]) {
    test(`installed equipment is not Product structured data on ${locale || "ka"} project page`, async ({ page }) => {
        const response = await page.goto(`${locale}/projects/${projectSlug}`, {
            waitUntil: "domcontentloaded",
        });
        expect(response?.status()).toBe(200);

        const scripts = await page.locator('script[type="application/ld+json"]').allTextContents();
        const nodes: unknown[] = [];
        const walk = (value: unknown): void => {
            if (Array.isArray(value)) {
                value.forEach(walk);
            } else if (value && typeof value === "object") {
                const object = value as Record<string, unknown>;
                nodes.push(object);
                Object.values(object).forEach(walk);
            }
        };
        for (const script of scripts) walk(JSON.parse(script));

        const equipmentNames = [
            "16 Port PoE Switch",
            "CAT6 SFTP Cable",
            "TVT 16-port NVR",
            "TVT FullColor 4MP Camera",
        ];
        const equipmentNodes = nodes.filter((node) => {
            if (!node || typeof node !== "object") return false;
            const name = (node as Record<string, unknown>).name;
            return typeof name === "string" && equipmentNames.some((item) =>
                name.toLowerCase().includes(item.toLowerCase()),
            );
        }) as Array<Record<string, unknown>>;
        expect(equipmentNodes.length).toBeGreaterThan(0);
        for (const node of equipmentNodes) {
            expect(node["@type"]).not.toBe("Product");
        }
    });
}
