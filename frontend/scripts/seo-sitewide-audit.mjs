const REQUIRED_HREFLANGS = ["ka-GE", "en-GE", "ru-GE", "x-default"];
const MAX_SITEMAP_PAGES = 250;
const CONCURRENCY = 6;

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
    const path = url.pathname === "/" ? "/" : url.pathname.replace(/\/$/, "");
    return `${url.origin}${path}${url.search}`;
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

function extractXmlLocs(xml) {
    return [...xml.matchAll(/<loc>([\s\S]*?)<\/loc>/gi)]
        .map((match) => match[1]?.trim())
        .filter(Boolean);
}

function toLocalPath(value, publicSiteUrl) {
    const target = new URL(value);
    const site = new URL(publicSiteUrl);
    if (target.origin !== site.origin) return null;
    return `${target.pathname}${target.search}`;
}

function logicalPath(path) {
    const normalized = path.replace(/^\/(?:en|ru)(?=\/|$)/, "");
    return normalized || "/";
}

async function request(baseUrl, path, userAgent, accept = "text/html,application/xhtml+xml") {
    const response = await fetch(`${baseUrl}${path}`, {
        redirect: "follow",
        signal: AbortSignal.timeout(15000),
        headers: {
            "user-agent": userAgent,
            accept,
        },
    });
    const body = await response.text();
    return { response, body };
}

function auditHtmlPage(body, path, publicSiteUrl) {
    const title = body.match(/<title>([\s\S]*?)<\/title>/i)?.[1]?.trim() || "";
    const description = metaValue(body, "name", "description");
    const canonical = attribute(linkByRel(body, "canonical") || "", "href");
    const robots = metaValue(body, "name", "robots");
    const h1Tags = tags(body, "h1");
    const htmlLang = attribute(tags(body, "html")[0] || "", "lang");
    const expectedCanonical = `${publicSiteUrl}${path === "/" ? "/" : path}`;
    const alternates = tags(body, "link")
        .filter((tag) => attribute(tag, "rel").toLowerCase().includes("alternate"))
        .map((tag) => attribute(tag, "hreflang"));
    const images = tags(body, "img");

    assert(title.length >= 10, `${path}: title is missing or too short`);
    assert(description.length >= 40, `${path}: meta description is missing or too short`);
    assert(canonical, `${path}: canonical is missing`);
    assert(
        normalizeUrl(canonical) === normalizeUrl(expectedCanonical),
        `${path}: canonical mismatch (${canonical} != ${expectedCanonical})`,
    );
    assert(!/noindex/i.test(robots), `${path}: sitemap URL must be indexable`);
    assert(h1Tags.length === 1, `${path}: expected exactly one H1, found ${h1Tags.length}`);
    assert(htmlLang, `${path}: html lang attribute is missing`);
    assert(visibleText(body).length >= 80, `${path}: rendered page content is too thin`);

    for (const hreflang of REQUIRED_HREFLANGS) {
        assert(alternates.includes(hreflang), `${path}: missing hreflang ${hreflang}`);
    }

    for (const image of images) {
        assert(/\balt\s*=/i.test(image), `${path}: image is missing an alt attribute`);
    }

    return { title, description };
}

async function mapConcurrent(items, worker, concurrency = CONCURRENCY) {
    let nextIndex = 0;
    const results = new Array(items.length);

    async function run() {
        while (true) {
            const index = nextIndex;
            nextIndex += 1;
            if (index >= items.length) return;
            results[index] = await worker(items[index], index);
        }
    }

    await Promise.all(
        Array.from({ length: Math.min(concurrency, items.length) }, () => run()),
    );

    return results;
}

function findDuplicates(records, key) {
    const values = new Map();
    for (const record of records) {
        const value = record[key]?.trim().toLocaleLowerCase();
        if (!value) continue;
        const paths = values.get(value) ?? [];
        paths.push(record.path);
        values.set(value, paths);
    }

    return [...values.entries()].filter(([, paths]) => {
        if (paths.length < 2) return false;
        return new Set(paths.map(logicalPath)).size > 1;
    });
}

export async function runSitewideSeoAudit({
    baseUrl,
    publicSiteUrl,
    googlebotUserAgent,
}) {
    const index = await request(
        baseUrl,
        "/sitemap.xml",
        googlebotUserAgent,
        "application/xml",
    );
    assert(index.response.status === 200, "sitewide audit: sitemap.xml must return HTTP 200");

    const sitemapUrls = extractXmlLocs(index.body).filter(
        (url) => !/sitemap-images\.xml(?:$|\?)/i.test(url),
    );
    assert(sitemapUrls.length > 0, "sitewide audit: sitemap index has no child sitemaps");

    const sitemapBodies = await mapConcurrent(sitemapUrls, async (url) => {
        const path = toLocalPath(url, publicSiteUrl);
        assert(path, `sitewide audit: foreign sitemap URL ${url}`);
        const result = await request(baseUrl, path, googlebotUserAgent, "application/xml");
        assert(result.response.status === 200, `${path}: sitemap must return HTTP 200`);
        return result.body;
    });

    const pageUrls = [...new Set(sitemapBodies.flatMap(extractXmlLocs))]
        .map((url) => toLocalPath(url, publicSiteUrl))
        .filter(Boolean)
        .slice(0, MAX_SITEMAP_PAGES);

    assert(pageUrls.length > 0, "sitewide audit: no indexable URLs found in sitemaps");

    const records = await mapConcurrent(pageUrls, async (path) => {
        const { response, body } = await request(baseUrl, path, googlebotUserAgent);
        const contentType = response.headers.get("content-type") || "";
        assert(response.status === 200, `${path}: sitemap URL returned HTTP ${response.status}`);
        assert(contentType.includes("text/html"), `${path}: sitemap URL must return HTML`);
        return { path, ...auditHtmlPage(body, path, publicSiteUrl) };
    });

    for (const [value, paths] of findDuplicates(records, "title")) {
        fail(`duplicate title across sitemap pages: ${paths.join(", ")} (${value})`);
    }

    for (const [value, paths] of findDuplicates(records, "description")) {
        fail(`duplicate meta description across sitemap pages: ${paths.join(", ")} (${value})`);
    }

    console.log(`Sitewide SEO audit passed for ${records.length} indexable sitemap URLs.`);
}
