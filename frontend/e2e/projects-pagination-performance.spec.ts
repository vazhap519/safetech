import { expect, test } from "@playwright/test";

const locales = [
    { prefix: "", canonicalPath: "/projects" },
    { prefix: "/en", canonicalPath: "/en/projects" },
    { prefix: "/ru", canonicalPath: "/ru/projects" },
];

for (const locale of locales) {
    test(`${locale.prefix || "ka"} projects stays within the initial HTML budget`, async ({
        page,
    }) => {
        const response = await page.goto(`${locale.prefix}/projects`, {
            waitUntil: "domcontentloaded",
        });

        expect(response?.status()).toBe(200);
        expect((await response!.body()).byteLength).toBeLessThan(200_000);
        await expect(page.locator("article")).toHaveCount(11);
        await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
            "href",
            new RegExp(`${locale.canonicalPath.replaceAll("/", "\\/")}$`),
        );
        await expect(page.locator('link[rel="next"]')).toHaveAttribute(
            "href",
            `${locale.canonicalPath}?page=2`,
        );
    });
}

test("second projects page exposes canonical and previous pagination links", async ({
    page,
}) => {
    const response = await page.goto("/projects?page=2", {
        waitUntil: "domcontentloaded",
    });

    expect(response?.status()).toBe(200);
    await expect(page.locator('link[rel="canonical"]')).toHaveAttribute(
        "href",
        /\/projects\?page=2$/,
    );
    await expect(page.locator('link[rel="prev"]')).toHaveAttribute(
        "href",
        "/projects",
    );
});
