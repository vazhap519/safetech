import { expect, test } from "@playwright/test";

import { getProjectVideo } from "../src/lib/project-video";
import { isIndexableProject, videoUrlset } from "../src/lib/sitemap";
import { getYouTubeEmbedUrl } from "../src/lib/youtube";

const slug = "qa-release-project";
const videoId = "JMO-UtBYR7Y";

for (const prefix of ["", "/en", "/ru"]) {
    test(`watch page is discoverable, localized and immediately playable at ${prefix || "/"}`, async ({ page, request, baseURL }) => {
        const path = `${prefix}/videos/${slug}`;
        const canonical = `${baseURL}${path}`;
        const response = await request.get(path);
        expect(response.status()).toBe(200);
        const html = await response.text();
        // The player must exist in the server HTML without a click or hydration.
        expect(html).toMatch(new RegExp(`<iframe[^>]+src="https://www.youtube-nocookie.com/embed/${videoId}`));

        await page.route("https://www.youtube-nocookie.com/**", (route) => route.fulfill({
            contentType: "text/html", body: "<p>Video player</p>",
        }));
        const errors: string[] = [];
        page.on("pageerror", (error) => errors.push(error.message));
        await page.goto(path, { waitUntil: "domcontentloaded" });
        await expect(page.locator("h1")).toBeVisible();
        await expect(page.locator('link[rel="canonical"]')).toHaveAttribute("href", canonical);
        await expect(page.locator('meta[property="og:type"]')).toHaveAttribute("content", "video.other");

        const player = page.locator('iframe[src*="youtube-nocookie.com/embed/"]');
        await expect(player).toHaveCount(1);
        await expect(player).toBeVisible();
        await expect(player).toHaveAttribute("loading", "eager");
        const rect = await player.boundingBox();
        expect(rect).not.toBeNull();
        expect(rect!.y + rect!.height).toBeLessThan(page.viewportSize()!.height);
        expect(rect!.width).toBeGreaterThan(page.viewportSize()!.width * 0.7);

        const data = await page.locator('script[type="application/ld+json"]').allTextContents();
        const graph = data.flatMap((value) => JSON.parse(value)["@graph"] || []);
        const video = graph.find((item) => item["@type"] === "VideoObject");
        expect(video).toMatchObject({
            url: canonical,
            name: await page.locator("h1").textContent(),
            thumbnailUrl: `https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`,
            mainEntityOfPage: { "@id": canonical },
        });
        expect(Number.isNaN(Date.parse(video.uploadDate))).toBe(false);
        expect(errors).toEqual([]);

        await page.locator(`article a[href="${prefix}/projects/${slug}"]`).click();
        await expect(page).toHaveURL(`${baseURL}${prefix}/projects/${slug}`);
        await expect(page.locator(`a[href="${path}"]`).first()).toBeVisible();
        await expect(page.locator('iframe[src*="youtube-nocookie.com/embed/"]')).toHaveCount(0);
        const articleSchema = (await page.locator('script[type="application/ld+json"]').allTextContents()).join("");
        expect(articleSchema).not.toContain('"@type":"VideoObject"');
    });
}

test("video sitemap lists the localized watch URLs and matching player metadata", async ({ request, baseURL }) => {
    const index = await request.get("/sitemap.xml");
    expect(await index.text()).toContain("/sitemap-videos.xml");
    const response = await request.get("/sitemap-videos.xml");
    expect(response.status()).toBe(200);
    const xml = await response.text();
    for (const prefix of ["", "/en", "/ru"]) {
        expect(xml).toContain(`<loc>${baseURL}${prefix}/videos/${slug}</loc>`);
    }
    expect(xml).toContain(`<video:thumbnail_loc>https://i.ytimg.com/vi/${videoId}/hqdefault.jpg</video:thumbnail_loc>`);
    expect(xml).toContain(`<video:player_loc>https://www.youtube-nocookie.com/embed/${videoId}?rel=0&amp;modestbranding=1</video:player_loc>`);
    expect(xml).not.toContain(`/projects/${slug}</loc>`);
});

test("missing watch pages return 404", async ({ request }) => {
    const response = await request.get("/videos/qa-missing-project");
    expect(response.status()).toBe(404);
});

test("video metadata rejects unrelated URLs and never invents a publication date", () => {
    const project = { slug: "sample", title: "CCTV & Wi-Fi", description: "<p>Camera restoration</p>", videoUrl: `https://youtube.com/shorts/${videoId}` };
    const video = getProjectVideo(project, "ka")!;
    expect(video.uploadDate).toBeUndefined();
    expect(video.description).not.toContain("<p>");
    expect(getProjectVideo({ ...project, publishedAt: "not-a-date" }, "en")!.uploadDate).toBeUndefined();
    expect(getProjectVideo({ ...project, videoUrl: "https://example.com/watch?v=JMO-UtBYR7Y" }, "en")).toBeNull();
    expect(getYouTubeEmbedUrl(`https://www.youtube-nocookie.com/embed/${videoId}`)).toBe(video.embedUrl);
    expect(getYouTubeEmbedUrl(`https://youtube.com.example.org/watch?v=${videoId}`)).toBe("");
    expect(isIndexableProject({ ...project, seo: { noindex: true } })).toBe(false);
    const xml = videoUrlset([{ loc: "https://safetech.ge/videos/sample", video }]);
    expect(xml).toContain("CCTV &amp; Wi-Fi");
    expect(xml).not.toContain("<video:publication_date>");
});
