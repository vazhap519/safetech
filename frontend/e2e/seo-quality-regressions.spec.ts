import { expect, test } from "@playwright/test";
import { buildImageSitemapItems, imageUrlset } from "../src/lib/sitemap";

test("image sitemap includes gallery photos once and excludes noindex projects", () => {
    const project = {
        slug: "verified-project", title: "Project & gallery", description: "Completed installation.",
        image: "/storage/cover.webp", imageAlt: "Main image", seo: { noindex: false },
        gallery: [{ src: "/storage/gallery.webp", alt: "Installed camera" }, { src: "/storage/cover.webp", alt: "Duplicate" }, { src: "" }],
    };
    const xml = imageUrlset(buildImageSitemapItems([], [project, { ...project, slug: "private-project", seo: { noindex: true } }]));
    expect(xml).toContain("gallery.webp");
    expect(xml.match(/<image:loc>/g)).toHaveLength(2);
    expect(xml).not.toContain("private-project");
    expect(xml).not.toContain("src=\"\"");
});

test("partial Local SEO translations are omitted from hreflang", async ({ page, request }) => {
    const api = process.env.E2E_API_BASE || "http://127.0.0.1:8000/api";
    const response = await request.get(`${api}/local-service-landings?view=summary`);
    const body = await response.json();
    const landing = body.data.find((item: { locationSlug: string }) => item.locationSlug === "qa-tbilisi");
    expect(landing).toBeTruthy();
    await page.goto(`/services/${landing.service.slug}/qa-tbilisi`);
    const languages = await page.locator('link[rel="alternate"][hreflang]').evaluateAll((links) => links.map((link) => link.getAttribute("hreflang")));
    expect(languages).toContain("ka-GE");
    expect(languages).not.toContain("en-GE");
    expect(languages).not.toContain("ru-GE");
});

test("installed project equipment keeps its name without Product offer markup", async ({ page }) => {
    await page.goto("/projects/qa-release-project");
    const scripts = await page.locator('script[type="application/ld+json"]').allTextContents();
    const nodes: Record<string, unknown>[] = [];
    const visit = (value: unknown) => {
        if (Array.isArray(value)) value.forEach(visit);
        else if (value && typeof value === "object") {
            nodes.push(value as Record<string, unknown>);
            Object.values(value).forEach(visit);
        }
    };
    scripts.forEach((script) => visit(JSON.parse(script)));
    const equipment = nodes.find((node) => node.name === "QA PoE camera");
    expect(equipment).toMatchObject({ "@type": "Thing", description: "2 — QA PoE camera" });
    expect(nodes.some((node) => node["@type"] === "Product")).toBe(false);
});
