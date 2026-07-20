import { getBaseUrl } from "@/lib/config";
import {
  DEFAULT_LOCALE,
  getLanguageTag,
  localizePath,
  supportedLocales,
} from "@/lib/locales";

const DEFAULT_API_BASE = "http://127.0.0.1:8000/api";

const sitemapHeaders = {
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

function normalizeBaseUrl() {
  return getBaseUrl().replace(/\/$/, "");
}

function getSitemapApiBase() {
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

function escapeXml(value = "") {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&apos;");
}

function absoluteUrl(pathOrUrl) {
  if (!pathOrUrl) return "";
  if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;

  const baseUrl = normalizeBaseUrl();
  const path = String(pathOrUrl).startsWith("/") ? pathOrUrl : `/${pathOrUrl}`;

  return `${baseUrl}${path}`;
}

export function localizedUrlEntries(path, metadata = {}) {
  const normalizedPath = String(path || "/").startsWith("/") ? path : `/${path}`;
  const defaultUrl = absoluteUrl(localizePath(normalizedPath, DEFAULT_LOCALE));
  const alternates = {
    ...Object.fromEntries(
      supportedLocales.map((locale) => [
        getLanguageTag(locale),
        absoluteUrl(localizePath(normalizedPath, locale)),
      ]),
    ),
    "x-default": defaultUrl,
  };

  return supportedLocales.map((locale) => ({
    ...metadata,
    loc: absoluteUrl(localizePath(normalizedPath, locale)),
    alternates,
  }));
}

function backendAssetUrl(pathOrUrl) {
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
    const res = await fetch(url, {
      cache: "no-store",
      signal: AbortSignal.timeout(5000),
    });
    if (!res.ok) return null;
    return await res.json();
  } catch {
    return null;
  }
}

function sitemapCollection(response) {
  if (Array.isArray(response)) return response;
  return Array.isArray(response?.data) ? response.data : [];
}

export async function categorySitemapResponse({ endpoint, pathPrefix, priority }) {
  const response = await safeFetchJson(buildSitemapApiUrl(endpoint));
  const urls = sitemapCollection(response)
    .filter((category) => category?.slug && !category?.noindex)
    .flatMap((category) => localizedUrlEntries(
      `${pathPrefix}/${encodeURIComponent(category.slug)}`,
      {
        ...(category.updated_at ? { lastmod: category.updated_at } : {}),
        changefreq: "weekly",
        priority,
      },
    ));

  return xmlResponse(urlset(urls));
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

export async function fetchImageSitemapItems() {
  const [services, projects, posts] = await Promise.all([
    fetchAllPaginated("/services"),
    fetchAllPaginated("/projects"),
    fetchAllPaginated("/blog"),
  ]);

  return [
    ...services.filter((service) => !service?.seo?.noindex).map((service) => ({
      loc: `${normalizeBaseUrl()}/services/${service.slug}`,
      image: backendAssetUrl(service.image),
      title: service.title || service.name,
    })),
    ...projects.filter((project) => !project?.seo?.noindex).map((project) => ({
      loc: `${normalizeBaseUrl()}/projects/${project.slug}`,
      image: backendAssetUrl(project.image),
      title: project.title || project.name,
    })),
    ...posts.filter((post) => !post?.meta?.noindex).map((post) => ({
      loc: `${normalizeBaseUrl()}/blog/${post.slug}`,
      image: backendAssetUrl(post.image),
      title: post.title,
    })),
  ].filter((item) => item.loc && item.image);
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
  const hasAlternates = urls.some((item) => item?.alternates);

  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"${hasAlternates ? '\n  xmlns:xhtml="http://www.w3.org/1999/xhtml"' : ""}>
${urls
  .filter((item) => item?.loc)
  .map(
    (item) => `  <url>
    <loc>${escapeXml(item.loc)}</loc>
    ${item.alternates ? Object.entries(item.alternates)
      .map(([hreflang, href]) => `<xhtml:link rel="alternate" hreflang="${escapeXml(hreflang)}" href="${escapeXml(href)}" />`)
      .join("\n    ") : ""}
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
      ${item.title ? `<image:title>${escapeXml(item.title)}</image:title>` : ""}
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
