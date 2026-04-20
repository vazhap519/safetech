import { getBaseUrl } from "@/lib/config";

export const sitemapHeaders = {
  "Content-Type": "application/xml; charset=utf-8",
  "Cache-Control": "public, max-age=300, s-maxage=300",
};

export function normalizeBaseUrl() {
  return getBaseUrl().replace(/\/$/, "");
}

export function escapeXml(value = "") {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&apos;");
}

export function absoluteUrl(pathOrUrl) {
  if (!pathOrUrl) return "";
  if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;

  const baseUrl = normalizeBaseUrl();
  const path = String(pathOrUrl).startsWith("/") ? pathOrUrl : `/${pathOrUrl}`;

  return `${baseUrl}${path}`;
}

export async function safeFetchJson(url) {
  try {
    const res = await fetch(url, { cache: "no-store" });
    if (!res.ok) return null;
    return await res.json();
  } catch {
    return null;
  }
}

export async function fetchAllPaginated(path, params = {}) {
  let page = 1;
  let lastPage = 1;
  const items = [];

  do {
    const query = new URLSearchParams({
      ...params,
      page: String(page),
    });

    const json = await safeFetchJson(
      `${process.env.NEXT_PUBLIC_API_URL}${path}?${query.toString()}`
    );

    const pageItems = json?.data?.services || json?.data || [];
    const meta = json?.data?.meta || json?.meta || {};

    if (Array.isArray(pageItems)) {
      items.push(...pageItems);
    }

    lastPage = Number(meta.last_page || 1);
    page += 1;
  } while (page <= lastPage);

  return items;
}

export function sitemapIndex(paths) {
  const baseUrl = normalizeBaseUrl();
  const now = new Date().toISOString();

  return `<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${paths
  .map(
    (path) => `  <sitemap>
    <loc>${escapeXml(`${baseUrl}${path}`)}</loc>
    <lastmod>${now}</lastmod>
  </sitemap>`
  )
  .join("\n")}
</sitemapindex>`;
}

export function urlset(urls) {
  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${urls
  .filter((item) => item?.loc)
  .map(
    (item) => `  <url>
    <loc>${escapeXml(item.loc)}</loc>
    ${item.lastmod ? `<lastmod>${escapeXml(item.lastmod)}</lastmod>` : ""}
    ${item.changefreq ? `<changefreq>${escapeXml(item.changefreq)}</changefreq>` : ""}
    ${item.priority ? `<priority>${escapeXml(item.priority)}</priority>` : ""}
  </url>`
  )
  .join("\n")}
</urlset>`;
}

export function imageUrlset(items) {
  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
${items
  .filter((item) => item?.loc && item?.image)
  .map(
    (item) => `  <url>
    <loc>${escapeXml(item.loc)}</loc>
    <image:image>
      <image:loc>${escapeXml(item.image)}</image:loc>
    </image:image>
  </url>`
  )
  .join("\n")}
</urlset>`;
}

export function xmlResponse(xml) {
  return new Response(xml, {
    headers: sitemapHeaders,
  });
}
