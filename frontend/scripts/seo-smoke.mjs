#!/usr/bin/env node

const baseUrl = (process.env.SEO_BASE_URL || "http://127.0.0.1:3000").replace(/\/$/, "");
const publicSiteUrl = (process.env.NEXT_PUBLIC_SITE_URL || "https://safetech.ge").replace(/\/$/, "");
const googlebotUserAgent =
    "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";

const coreRoutes = [
    { path: "/", locale: "ka" },
    { path: "/about", locale: "ka" },
    { path: "/services", locale: "ka" },
    { path: "/projects", locale: "ka" },
    { path: "/contact", locale: "ka" },
    { path: "/en", locale: "en" },
    { path: "/ru", locale: "ru" },
];

const removedRoutes = ["/blog", "/shop", "/service-calculator", "/privacy"];
const requiredHreflangs = ["ka", "en", "ru", "x-default"];

function fail(message) {
    throw new Error(message);
}

function assert(condition, message) {
    if (!condition) fail(message);
}

function tags(html, name) {
    return [...html.matchAll(new RegExp(`<${name}\\b[^>]*>`, "gi"))].map(
        (match) => match[0],
    );
}

function attribute(tag, name) {
    const match = tag.match(
        new RegExp(`\\b${name}\\s*=\\s*["']([^"']*)["']`, "i"),
    );

    return match?.[1] || "";
}

function metaContent(html, name) {
    const tag = tags(html, "meta").find(
        (item) => attribute(item, "name").toLowerCase() === name.toLowerCase(),
    );

    return tag ? attribute(tag, "content") : "";
}

function linkByRel(html, rel) {
    return tags(html, "link").find((item) =>
        attribute(item, "rel")
            .toLowerCase()
            .split(/\s+/)
            .includes(rel.toLowerCase()),
    );
}

function normalizeUrl(value) {
    const url = new URL(value);
    const normalizedPath = url.pathname === "/" ? "/" : url.pathname.replace(/\/$/, "");

    return `${url.origin}${normalizedPath}${url.search}`;
}

function visibleText(html) {
    return html
        .replace(/<script\b[\s\S]*?<\/script>/gi, " ")
        .replace(/<style\b[\s\S]*?<\/style>/gi, " ")
        .replace(/<[^>]+>/g, " ")
        .replace(/&nbsp;|&#160;/gi, " ")
        .replace(/&[a-z0-9#]+;/gi, " ")
        .replace(/\s+/g, " ")
        .trim();
}

function validateJsonLd(html, path) {
    const scripts = [
        ...html.matchAll(
            /<script\b[^>]*type=["']application\/ld\+json["'][^>]*>([\s\S]*?)<\/script>/gi,
        ),
    ];

    assert(scripts.length > 0, `${path}: missing JSON-LD structured data`);

    for (const [, payload] of scripts) {
        try {
            JSON.parse(payload.trim());
        } catch (error) {
            fail(`${path}: invalid JSON-LD (${error.message})`);
        }
    }
}

async function request(path, options = {}) {
    const startedAt = performance.now();
    const response = await fetch(`${baseUrl}${path}`, {
        redirect: "follow",
        signal: AbortSignal.timeout(15000),
        headers: {
            "user-agent": googlebotUserAgent,
            accept: options.accept || "text/html,application/xhtml+xml",
        },
    });
    const body = await response.text();
    const elapsed = Math.round(performance.now() - startedAt);

    console.log(`${response.status} ${path} (${elapsed} ms)`);

    return { response, body, elapsed };
}

async function validateCorePage({ path, locale }) {
    const { response, body } = await request(path);
    const contentType = response.headers.get("content-type") || "";

    assert(response.status === 200, `${path}: expected HTTP 200, got ${response.status}`);
    assert(contentType.includes("text/html"), `${path}: expected HTML content type`);
    assert(!/noindex/i.test(response.headers.get("x-robots-tag") || ""), `${path}: X-Robots-Tag blocks indexing`);

    const htmlTag = body.match(/<html\b[^>]*>/i)?.[0] || "";
    assert(attribute(htmlTag, "lang") === locale, `${path}: expected html lang=${locale}`);

    const title = body.match(/<title\b[^>]*>([\s\S]*?)<\/title>/i)?.[1]?.trim() || "";
    assert(title.length >= 10, `${path}: missing or too-short title`);

    const description = metaContent(body, "description");
    assert(description.length >= 50, `${path}: missing or too-short meta description`);

    const robots = `${metaContent(body, "robots")} ${metaContent(body, "googlebot")}`;
    assert(!/\bnoindex\b/i.test(robots), `${path}: page metadata contains noindex`);
    assert(!/\bnofollow\b/i.test(robots), `${path}: page metadata contains nofollow`);

    const canonicalTag = linkByRel(body, "canonical");
    const canonical = canonicalTag ? attribute(canonicalTag, "href") : "";
    const expectedCanonical = normalizeUrl(`${publicSiteUrl}${path}`);
    assert(canonical, `${path}: missing canonical URL`);
    assert(
        normalizeUrl(canonical) === expectedCanonical,
        `${path}: canonical mismatch (${canonical} != ${expectedCanonical})`,
    );

    const alternateTags = tags(body, "link").filter((item) =>
        attribute(item, "rel").toLowerCase().split(/\s+/).includes("alternate"),
    );
    const hreflangs = new Set(
        alternateTags.map((item) => attribute(item, "hreflang")).filter(Boolean),
    );

    for (const hreflang of requiredHreflangs) {
        assert(hreflangs.has(hreflang), `${path}: missing hreflang ${hreflang}`);
    }

    const h1Count = (body.match(/<h1\b/gi) || []).length;
    assert(h1Count === 1, `${path}: expected exactly one H1, got ${h1Count}`);
    assert(visibleText(body).length >= 120, `${path}: insufficient indexable text`);

    validateJsonLd(body, path);

    assert(response.headers.has("content-security-policy"), `${path}: missing CSP header`);
    assert(
        response.headers.get("x-content-type-options") === "nosniff",
        `${path}: missing X-Content-Type-Options`,
    );
}

async function validateRobots() {
    const { response, body } = await request("/robots.txt", { accept: "text/plain" });

    assert(response.status === 200, `robots.txt: expected HTTP 200, got ${response.status}`);
    assert(/user-agent:\s*\*/i.test(body), "robots.txt: missing wildcard user-agent");
    assert(/allow:\s*\//i.test(body), "robots.txt: missing root allow rule");
    assert(!/^disallow:\s*\/\s*$/im.test(body), "robots.txt: site-wide crawl block detected");
    assert(
        new RegExp(`sitemap:\\s*${publicSiteUrl.replace(/[.*+?^${}()|[\]\\]/g, "\\$&")}/sitemap\\.xml`, "i").test(body),
        "robots.txt: missing canonical sitemap declaration",
    );
}

async function validateSitemaps() {
    const index = await request("/sitemap.xml", { accept: "application/xml" });
    assert(index.response.status === 200, "sitemap.xml: expected HTTP 200");
    assert(/<sitemapindex\b/i.test(index.body), "sitemap.xml: invalid sitemap index");
    assert(/\/sitemap-main\.xml/i.test(index.body), "sitemap.xml: missing main sitemap");

    const main = await request("/sitemap-main.xml", { accept: "application/xml" });
    assert(main.response.status === 200, "sitemap-main.xml: expected HTTP 200");
    assert(/<urlset\b/i.test(main.body), "sitemap-main.xml: invalid URL set");

    for (const route of ["/", "/about", "/services", "/projects", "/contact"]) {
        const expected = `${publicSiteUrl}${route === "/" ? "/" : route}`;
        assert(main.body.includes(`<loc>${expected}</loc>`), `sitemap-main.xml: missing ${expected}`);
    }

    for (const hreflang of requiredHreflangs) {
        assert(
            main.body.includes(`hreflang="${hreflang}"`),
            `sitemap-main.xml: missing hreflang ${hreflang}`,
        );
    }
}

async function validateRemovedRoutes() {
    for (const path of removedRoutes) {
        const { response } = await request(path);
        assert(response.status === 404, `${path}: expected HTTP 404, got ${response.status}`);
    }
}

async function main() {
    for (const route of coreRoutes) {
        await validateCorePage(route);
    }

    await validateRobots();
    await validateSitemaps();
    await validateRemovedRoutes();

    console.log("Google SEO smoke checks passed.");
}

main().catch((error) => {
    console.error(error.stack || error.message || error);
    process.exitCode = 1;
});
