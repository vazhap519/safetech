#!/usr/bin/env node

const baseUrl = (process.env.SEO_BASE_URL || "http://127.0.0.1:3000").replace(/\/$/, "");
const publicSiteUrl = (process.env.NEXT_PUBLIC_SITE_URL || "https://safetech.ge").replace(/\/$/, "");
const googlebotUserAgent =
    "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)";

const coreRoutes = [
    { path: "/", languageTag: "ka-GE", ogLocale: "ka_GE" },
    { path: "/about", languageTag: "ka-GE", ogLocale: "ka_GE" },
    { path: "/services", languageTag: "ka-GE", ogLocale: "ka_GE" },
    { path: "/projects", languageTag: "ka-GE", ogLocale: "ka_GE" },
    { path: "/contact", languageTag: "ka-GE", ogLocale: "ka_GE" },
    { path: "/en", languageTag: "en-GE", ogLocale: "en_GE" },
    { path: "/ru", languageTag: "ru-GE", ogLocale: "ru_GE" },
];

const removedRoutes = ["/blog", "/shop", "/privacy"];
const requiredHreflangs = ["ka-GE", "en-GE", "ru-GE", "x-default"];

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

function metaValue(html, attributeName, attributeValue) {
    const tag = tags(html, "meta").find(
        (item) =>
            attribute(item, attributeName).toLowerCase() ===
            attributeValue.toLowerCase(),
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
    const normalizedPath =
        url.pathname === "/" ? "/" : url.pathname.replace(/\/$/, "");

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

function targetsGeorgia(value) {
    if (Array.isArray(value)) return value.some(targetsGeorgia);
    if (!value || typeof value !== "object") return false;

    const areaServed = value.areaServed;
    const areaTargetsGeorgia =
        areaServed === "GE" ||
        areaServed === "Georgia" ||
        (areaServed &&
            typeof areaServed === "object" &&
            (areaServed.name === "Georgia" || areaServed.identifier === "GE"));

    return areaTargetsGeorgia || Object.values(value).some(targetsGeorgia);
}

function validateJsonLd(html, path) {
    const scripts = [
        ...html.matchAll(
            /<script\b[^>]*type=["']application\/ld\+json["'][^>]*>([\s\S]*?)<\/script>/gi,
        ),
    ];

    assert(scripts.length > 0, `${path}: missing JSON-LD structured data`);

    const schemas = scripts.map(([, payload]) => {
        try {
            return JSON.parse(payload.trim());
        } catch (error) {
            fail(`${path}: invalid JSON-LD (${error.message})`);
        }
    });

    assert(
        schemas.some(targetsGeorgia),
        `${path}: structured data does not target Georgia`,
    );
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

    return { response, body };
}

async function validateCorePage({ path, languageTag, ogLocale }) {
    const { response, body } = await request(path);
    const contentType = response.headers.get("content-type") || "";

    assert(response.status === 200, `${path}: expected HTTP 200, got ${response.status}`);
    assert(contentType.includes("text/html"), `${path}: expected HTML response`);

    const title = body.match(/<title>([\s\S]*?)<\/title>/i)?.[1]?.trim() || "";
    const description = metaValue(body, "name", "description");
    const canonical = attribute(linkByRel(body, "canonical") || "", "href");
    const robots = metaValue(body, "name", "robots");
    const ogTitle = metaValue(body, "property", "og:title");
    const ogDescription = metaValue(body, "property", "og:description");
    const ogUrl = metaValue(body, "property", "og:url");
    const ogLocaleValue = metaValue(body, "property", "og:locale");
    const text = visibleText(body);
    const htmlLang = attribute(tags(body, "html")[0] || "", "lang");
    const expectedCanonical = `${publicSiteUrl}${path === "/" ? "/" : path}`;

    assert(title.length >= 10, `${path}: title is missing or too short`);
    assert(description.length >= 50, `${path}: meta description is missing or too short`);
    assert(canonical, `${path}: canonical is missing`);
    assert(
        normalizeUrl(canonical) === normalizeUrl(expectedCanonical),
        `${path}: canonical mismatch (${canonical} != ${expectedCanonical})`,
    );
    assert(
        !/noindex/i.test(robots),
        `${path}: core page must remain indexable`,
    );
    assert(ogTitle, `${path}: missing og:title`);
    assert(ogDescription, `${path}: missing og:description`);
    assert(
        normalizeUrl(ogUrl) === normalizeUrl(expectedCanonical),
        `${path}: og:url mismatch`,
    );
    assert(
        ogLocaleValue === ogLocale,
        `${path}: og:locale mismatch (${ogLocaleValue} != ${ogLocale})`,
    );
    assert(
        htmlLang === languageTag,
        `${path}: html lang mismatch (${htmlLang} != ${languageTag})`,
    );
    assert(text.length >= 50, `${path}: rendered page content is too thin`);

    const alternates = tags(body, "link")
        .filter((tag) => attribute(tag, "rel").toLowerCase().includes("alternate"))
        .map((tag) => attribute(tag, "hreflang"));

    for (const hreflang of requiredHreflangs) {
        assert(
            alternates.includes(hreflang),
            `${path}: missing hreflang ${hreflang}`,
        );
    }

    validateJsonLd(body, path);
}

async function validateRobots() {
    const { response, body } = await request("/robots.txt", {
        accept: "text/plain",
    });

    assert(response.status === 200, `robots.txt: expected HTTP 200, got ${response.status}`);
    assert(/user-agent:\s*\*/i.test(body), "robots.txt: missing wildcard user-agent");
    assert(/allow:\s*\//i.test(body), "robots.txt: missing root allow rule");
    assert(!/^disallow:\s*\/\s*$/im.test(body), "robots.txt: site-wide crawl block detected");
    assert(
        body.includes(`Sitemap: ${publicSiteUrl}/sitemap.xml`),
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

async function validateLegacyCalculatorRedirect() {
    const path = "/service-calculator?service=custom-computer-build";
    const startedAt = performance.now();
    const response = await fetch(`${baseUrl}${path}`, {
        redirect: "manual",
        signal: AbortSignal.timeout(15000),
        headers: {
            "user-agent": googlebotUserAgent,
            accept: "text/html,application/xhtml+xml",
        },
    });
    const elapsed = Math.round(performance.now() - startedAt);
    const location = response.headers.get("location") || "";

    console.log(`${response.status} ${path} -> ${location} (${elapsed} ms)`);

    assert(
        response.status === 307 || response.status === 308,
        `${path}: expected HTTP redirect, got ${response.status}`,
    );
    assert(
        location.includes("/services?service=custom-computer-build"),
        `${path}: redirect does not preserve the selected service (${location})`,
    );
    assert(
        location.includes("#service-calculator"),
        `${path}: redirect does not target the embedded calculator (${location})`,
    );
}

async function main() {
    assert(
        new URL(publicSiteUrl).hostname.endsWith(".ge"),
        "NEXT_PUBLIC_SITE_URL must use Georgia's .ge country-code domain",
    );

    for (const route of coreRoutes) {
        await validateCorePage(route);
    }

    await validateRobots();
    await validateSitemaps();
    await validateRemovedRoutes();
    await validateLegacyCalculatorRedirect();

    console.log("Google SEO and Georgia targeting smoke checks passed.");
}

main().catch((error) => {
    console.error(error.stack || error.message || error);
    process.exitCode = 1;
});
