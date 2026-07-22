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
  for (let attempt = 0; attempt < 2; attempt += 1) {
    try {
      const res = await fetch(url, {
        cache: "no-store",
        signal: AbortSignal.timeout(8000),
      });

      if (res.ok) return await res.json();
      if (res.status < 500 && res.status !== 429) return null;
    } catch {
      // Retry transient network failures once before failing the sitemap request.
    }

    if (attempt === 0) {
      await new Promise((resolve) => setTimeout(resolve, 150));
    }
  }

  return null;
}

function sitemapCollection(response) {
  if (Array.isArray(response)) return response;
  return Array.isArray(response?.data) ? response.data : [];
}

function hasEligibleCategory(response, content, categorySlug) {
  const eligibleCategorySlugs = new Set(
    content
      .map(categorySlug)
      .filter(hasValidSitemapSlug),
  );

  return sitemapCollection(response)
    .filter(isIndexableCategory)
    .some((category) => eligibleCategorySlugs.has(category.slug));
}

function meaningfulText(value) {
  if (typeof value === "string") {
    return value
      .replace(/<[^>]*>/g, " ")
      .replace(/&(?:nbsp|#160);/gi, " ")
      .replace(/\s+/g, " ")
      .trim();
  }

  if (Array.isArray(value)) {
    return value.map(meaningfulText).filter(Boolean).join(" ");
  }

  if (value && typeof value === "object") {
    return Object.values(value).map(meaningfulText).filter(Boolean).join(" ");
  }

  return "";
}

export function hasMeaningfulContent(...values) {
  return values.some((value) => meaningfulText(value).length > 0);
}

export function hasValidSitemapSlug(value) {
  return typeof value === "string"
    && value.trim() === value
    && value.length > 0
    && !/[/?#\s]/.test(value);
}

export function isIndexableService(service) {
  return Boolean(
    hasValidSitemapSlug(service?.slug)
    && !service?.seo?.noindex
    && hasMeaningfulContent(service?.title, service?.name)
    && hasMeaningfulContent(
      service?.description,
      service?.shortDescription,
      service?.longDescription,
      service?.overview,
      service?.benefits,
      service?.solutions,
      service?.features,
      service?.process,
      service?.warranty,
      service?.sla,
    )
  );
}

export function isIndexableProject(project) {
  return Boolean(
    hasValidSitemapSlug(project?.slug)
    && !project?.seo?.noindex
    && hasMeaningfulContent(project?.title, project?.name)
    && hasMeaningfulContent(
      project?.description,
      project?.overview,
      project?.scope,
      project?.challenges,
      project?.solutions,
      project?.process,
      project?.results,
    )
  );
}

export function isIndexableBlogPost(post) {
  return Boolean(
    hasValidSitemapSlug(post?.slug)
    && !post?.meta?.noindex
    && hasMeaningfulContent(post?.title)
    && (
      post?.has_content === true
      || hasMeaningfulContent(post?.excerpt, post?.body, post?.sections)
    )
  );
}

export function isIndexableCategory(category) {
  return Boolean(
    hasValidSitemapSlug(category?.slug)
    && !category?.noindex
    && hasMeaningfulContent(category?.name)
  );
}

export async function categorySitemapResponse({
  endpoint,
  pathPrefix,
  priority,
  contentEndpoint,
  contentFilter,
  categorySlug,
}) {
  const [response, content] = await Promise.all([
    safeFetchJson(buildSitemapApiUrl(endpoint)),
    contentEndpoint ? fetchAllPaginated(contentEndpoint) : Promise.resolve([]),
  ]);

  if (!response) {
    throw new Error(`Unable to load sitemap categories from ${endpoint}`);
  }

  const eligibleCategorySlugs = contentEndpoint
    ? new Set(
      content
        .filter((item) => contentFilter?.(item) ?? true)
        .map((item) => categorySlug?.(item))
        .filter(hasValidSitemapSlug),
    )
    : null;
  const urls = sitemapCollection(response)
    .filter((category) => isIndexableCategory(category))
    .filter((category) => !eligibleCategorySlugs || eligibleCategorySlugs.has(category.slug))
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

export async function fetchPaginatedPages(path, params = {}) {
  let page = 1;
  let lastPage = 1;
  const items = [];
  const pages = [];

  do {
    const query = {
      ...params,
      page: String(page),
    };

    const json = await safeFetchJson(buildSitemapApiUrl(path, query));

    if (!json) {
      throw new Error(`Unable to load sitemap content from ${path}, page ${page}`);
    }

    const pageItems = json?.data?.services || json?.data || [];
    const meta = json?.data?.meta || json?.meta || {};

    if (Array.isArray(pageItems)) {
      items.push(...pageItems);
      pages.push({ page, items: pageItems });
    }

    const reportedLastPage = Number(meta.last_page || 1);
    lastPage = Number.isFinite(reportedLastPage)
      ? Math.max(1, Math.min(reportedLastPage, 1000))
      : 1;
    page += 1;
  } while (page <= lastPage);

  return { items, pages, lastPage };
}

export async function fetchAllPaginated(path, params = {}) {
  return (await fetchPaginatedPages(path, params)).items;
}

function buildImageSitemapItems(services, projects, posts) {
  return [
    ...services.filter(isIndexableService).map((service) => ({
      loc: `${normalizeBaseUrl()}/services/${encodeURIComponent(service.slug)}`,
      image: backendAssetUrl(service.image),
      title: service.title || service.name,
    })),
    ...projects.filter(isIndexableProject).map((project) => ({
      loc: `${normalizeBaseUrl()}/projects/${encodeURIComponent(project.slug)}`,
      image: backendAssetUrl(project.image),
      title: project.title || project.name,
    })),
    ...posts.filter(isIndexableBlogPost).map((post) => ({
      loc: `${normalizeBaseUrl()}/blog/${encodeURIComponent(post.slug)}`,
      image: backendAssetUrl(post.image),
      title: post.title,
    })),
  ].filter((item) => item.loc && item.image);
}

export async function fetchImageSitemapItems() {
  const [services, projects, posts] = await Promise.all([
    fetchAllPaginated("/services"),
    fetchAllPaginated("/projects"),
    fetchAllPaginated("/blog"),
  ]);

  return buildImageSitemapItems(services, projects, posts);
}

export async function getSitemapIndexPaths() {
  const [
    services,
    projects,
    posts,
    serviceCategoriesResponse,
    projectCategoriesResponse,
    blogCategoriesResponse,
  ] = await Promise.all([
    fetchAllPaginated("/services"),
    fetchAllPaginated("/projects"),
    fetchAllPaginated("/blog"),
    safeFetchJson(buildSitemapApiUrl("/service-categories")),
    safeFetchJson(buildSitemapApiUrl("/project-categories")),
    safeFetchJson(buildSitemapApiUrl("/categories")),
  ]);

  if (!serviceCategoriesResponse
    || !projectCategoriesResponse
    || !blogCategoriesResponse) {
    throw new Error("Unable to load categories for the sitemap index");
  }

  const indexableServices = services.filter(isIndexableService);
  const indexableProjects = projects.filter(isIndexableProject);
  const indexablePosts = posts.filter(isIndexableBlogPost);
  const paths = ["/sitemap-main.xml"];

  if (indexableServices.length) paths.push("/sitemap-services.xml");
  if (hasEligibleCategory(
    serviceCategoriesResponse,
    indexableServices,
    (service) => service?.category?.slug,
  )) {
    paths.push("/sitemap-service-categories.xml");
  }
  if (indexablePosts.length) paths.push("/sitemap-blog.xml");
  if (hasEligibleCategory(
    blogCategoriesResponse,
    indexablePosts,
    (post) => post?.category?.slug,
  )) {
    paths.push("/sitemap-blog-categories.xml");
  }
  if (indexableProjects.length) paths.push("/sitemap-projects.xml");
  if (hasEligibleCategory(
    projectCategoriesResponse,
    indexableProjects,
    (project) => project?.category,
  )) {
    paths.push("/sitemap-project-categories.xml");
  }
  if (buildImageSitemapItems(
    indexableServices,
    indexableProjects,
    indexablePosts,
  ).length) {
    paths.push("/sitemap-images.xml");
  }

  return paths;
}

export function sitemapIndex(paths) {
  const baseUrl = normalizeBaseUrl();

  return `<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
${paths
  .map(
    (path) => `  <sitemap>
    <loc>${escapeXml(`${baseUrl}${path}`)}</loc>
  </sitemap>`
  )
  .join("\n")}
</sitemapindex>`;
}

export function urlset(urls) {
  const uniqueUrls = Array.from(
    new Map(urls.filter((item) => item?.loc).map((item) => [item.loc, item])).values(),
  );
  const hasAlternates = uniqueUrls.some((item) => item?.alternates);

  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"${hasAlternates ? '\n  xmlns:xhtml="http://www.w3.org/1999/xhtml"' : ""}>
${uniqueUrls
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
  const uniqueItems = Array.from(
    new Map(
      items
        .filter((item) => item?.loc && item?.image)
        .map((item) => [`${item.loc}\n${item.image}`, item]),
    ).values(),
  );

  return `<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
${uniqueItems
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
