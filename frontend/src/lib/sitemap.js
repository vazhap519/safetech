import { getBaseUrl } from "@/lib/config";

const DEFAULT_API_BASE = "http://127.0.0.1:8000/api";

export const sitemapHeaders = {
  "Content-Type": "application/xml; charset=utf-8",
  "Cache-Control": "public, max-age=300, s-maxage=300",
};

function normalizeApiBase(value) {
  if (!value) return null;

  const trimmed = String(value).trim();
  if (!trimmed) return null;

  try {
    return new URL(trimmed).toString().replace(/\/$/, "");
  } catch {
    return null;
  }
}

export function normalizeBaseUrl() {
  return getBaseUrl().replace(/\/$/, "");
}

export function getSitemapApiBase() {
  return (
    normalizeApiBase(process.env.BACKEND_API_URL) ||
    normalizeApiBase(process.env.NEXT_PUBLIC_API_URL) ||
    DEFAULT_API_BASE
  );
}

export function buildSitemapApiUrl(path, params = {}) {
  const normalizedPath = String(path).startsWith("/") ? path : `/${path}`;
  const url = new URL(`${getSitemapApiBase()}${normalizedPath}`);

  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== "") {
      url.searchParams.set(key, String(value));
    }
  });

  return url.toString();
}

export function getLastPage(json) {
  return Number(json?.data?.meta?.last_page || json?.meta?.last_page || 1);
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

export function backendAssetUrl(pathOrUrl) {
  if (!pathOrUrl) return "";
  if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;

  const value = String(pathOrUrl);

  if (value.startsWith("/storage") || value.startsWith("/uploads")) {
    return `${new URL(getSitemapApiBase()).origin}${value}`;
  }

  if (value.startsWith("/")) {
    return absoluteUrl(value);
  }

  return `${new URL(getSitemapApiBase()).origin}/storage/${value.replace(/^\/+/, "")}`;
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
    const query = {
      ...params,
      page: String(page),
    };

    const json = await safeFetchJson(buildSitemapApiUrl(path, query));

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
