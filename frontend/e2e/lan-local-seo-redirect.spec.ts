import { expect, test } from "@playwright/test";

const cities = ["tbilisi", "bakuriani", "surami", "borjomi", "khashuri", "abastumani"];
const locales = ["", "/en", "/ru"];

for (const locale of locales) {
    for (const city of cities) {
        for (const legacySlug of ["lan-installation", "lan-network-installation"]) {
            test(`${locale || "/"} ${legacySlug}/${city} redirects to the published cable service`, async ({ request }) => {
                const response = await request.get(
                    `${locale}/services/${legacySlug}/${city}`,
                    { maxRedirects: 0 },
                );

                expect(response.status()).toBe(308);
                const location = response.headers()["location"];
                expect(new URL(location).pathname).toBe(
                    `${locale}/services/network-cable-installation/${city}`,
                );
            });
        }
    }
}
