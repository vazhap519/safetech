import "server-only";

import { buildServerApiUrl } from "@/lib/backend-api";

async function fetcher(url, options = {}) {
  try {
    const configuredNext = options.next || {};
    const tags = Array.from(new Set(["cms", ...(configuredNext.tags || [])]));
    const res = await fetch(url, {
      ...options,
      next: {
        revalidate: 60,
        ...configuredNext,
        tags,
      },
    });
    const text = await res.text();
    const data = text ? JSON.parse(text) : null;

    if (!res.ok) {
      if (process.env.NODE_ENV !== "production") {
        console.error("API error:", {
          url,
          status: res.status,
          body: data,
        });
      }

      return {
        error: true,
        status: res.status,
        data,
      };
    }

    return data;
  } catch (error) {
    if (process.env.NODE_ENV !== "production") {
      console.error("Fetch failed:", error);
    }

    return {
      error: true,
      message: error instanceof Error ? error.message : "fetch failed",
    };
  }
}

function buildUrl(path, params = {}) {
  const query = new URLSearchParams(params).toString();
  const url = buildServerApiUrl(path);

  return `${url}${query ? `?${query}` : ""}`;
}

export const getPrivacy = ({ locale, ...options } = {}) =>
  fetcher(buildUrl("/privacy", locale ? { locale } : {}), {
    next: { tags: ["privacy"] },
    ...options,
  });

export const getBlog = ({ page = 1, category = "all", locale } = {}, options = {}) =>
  fetcher(
    buildUrl("/blog", {
      page,
      ...(locale ? { locale } : {}),
      ...(category !== "all" ? { category } : {}),
    }),
    {
      next: { tags: ["blog"] },
      ...options,
    },
  );

export const getBlogPost = (slug, { locale, ...options } = {}) =>
  fetcher(buildUrl(`/blog/${slug}`, locale ? { locale } : {}), {
    next: { tags: [`post-${slug}`] },
    ...options,
  });

export const getCategories = ({ locale, ...options } = {}) =>
  fetcher(buildUrl("/categories", locale ? { locale } : {}), {
    next: { tags: ["categories"] },
    ...options,
  });

export const getServiceCategories = ({ locale, ...options } = {}) =>
  fetcher(buildUrl("/service-categories", locale ? { locale } : {}), {
    next: { tags: ["service-categories"] },
    ...options,
  });

export const getProjectCategories = ({ locale, ...options } = {}) =>
  fetcher(buildUrl("/project-categories", locale ? { locale } : {}), {
    next: { tags: ["project-categories"] },
    ...options,
  });
