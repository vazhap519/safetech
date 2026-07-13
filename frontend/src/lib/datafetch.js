const API = process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

async function fetcher(url, options = {}) {
  try {
    const res = await fetch(url, {
      ...options,
      next: options.next || { revalidate: 60 },
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
  return `${API}${path}${query ? `?${query}` : ""}`;
}

export const getHome = (options = {}) =>
  fetcher(buildUrl("/home"), {
    next: { tags: ["home"] },
    ...options,
  });

export const getServices = ({ page = 1 } = {}, options = {}) =>
  fetcher(buildUrl("/services", { page }), {
    next: { tags: ["services"] },
    ...options,
  });

export const getService = (slug, options = {}) =>
  fetcher(buildUrl(`/services/${slug}`), {
    next: { tags: [`service-${slug}`] },
    ...options,
  });

export const getSettings = (options = {}) =>
  fetcher(buildUrl("/settings"), {
    next: { tags: ["settings"] },
    ...options,
  });

export const getPrivacy = ({ locale, ...options } = {}) =>
  fetcher(buildUrl("/privacy", locale ? { locale } : {}), {
    next: { tags: ["privacy"] },
    ...options,
  });

export const getAbout = (options = {}) =>
  fetcher(buildUrl("/about"), {
    next: { tags: ["about"] },
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
