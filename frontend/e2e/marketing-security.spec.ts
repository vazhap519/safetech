import { expect, test } from "@playwright/test";

test("CSP permits the consented Google Ads endpoints used by configured tags", async ({
    request,
}) => {
    const response = await request.get("/");
    const policy = response.headers()["content-security-policy"] || "";

    expect(response.status()).toBe(200);
    expect(policy).toContain(
        "script-src 'self' 'unsafe-inline' https://www.googletagmanager.com https://googleads.g.doubleclick.net",
    );
    expect(policy).toContain("img-src 'self' data: blob:");
    expect(policy).toContain("https://ad.doubleclick.net");
    expect(policy).toContain("https://googleads.g.doubleclick.net");
    expect(policy).toContain("connect-src 'self'");
});
