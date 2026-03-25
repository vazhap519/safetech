const API =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

/* =========================
   🔥 FETCHER (BALANCED)
========================= */
async function fetcher(url, options = {}) {
  try {
    const res = await fetch(url, {
      ...options,
      next: options.next || { revalidate: 60 },
    });

    const text = await res.text();

    let data = null;
    try {
      data = text ? JSON.parse(text) : null;
    } catch (e) {
      console.warn("⚠️ JSON parse failed:", text);
    }

    if (!res.ok) {
      console.error("❌ API ERROR:", {
        url,
        status: res.status,
        body: text,
      });

      // ❗ არ ვაგდებთ error-ს → UI გადაწყვეტს
      return {
        error: true,
        status: res.status,
        data,
      };
    }

    return data;

  } catch (error) {
    console.error("❌ FETCH FAILED:", error);

    return {
      error: true,
      message: error.message,
    };
  }
}

/* =========================
   🔧 QUERY BUILDER
========================= */
function buildUrl(path, params = {}) {
  const query = new URLSearchParams(params).toString();
  return `${API}${path}${query ? `?${query}` : ""}`;
}

/* =========================
   🔵 HOME
========================= */
export const getHome = (options = {}) =>
  fetcher(buildUrl(`/`), {
    next: { tags: ["home"] },
    ...options,
  });

/* =========================
   🟢 SERVICES
========================= */

// LIST
export const getServices = ({ page = 1 } = {}, options = {}) =>
  fetcher(buildUrl(`/services`, { page }), {
    next: { tags: ["services"] },
    ...options,
  });

// SINGLE
export const getService = (slug, options = {}) =>
  fetcher(buildUrl(`/services/${slug}`), {
    next: { tags: [`service-${slug}`] },
    ...options,
  });

/* =========================
   ⚙ SETTINGS
========================= */
export const getSettings = (options = {}) =>
  fetcher(buildUrl(`/settings`), {
    next: { tags: ["settings"] },
    ...options,
  });

/* =========================
   🔒 PRIVACY
========================= */
export const getPrivacy = (options = {}) =>
  fetcher(buildUrl(`/privacy`), {
    next: { tags: ["privacy"] },
    ...options,
  });

/* =========================
   📘 ABOUT
========================= */
export const getAbout = (options = {}) =>
  fetcher(buildUrl(`/about`), {
    next: { tags: ["about"] },
    ...options,
  });

/* =========================
   📰 BLOG
========================= */

// LIST
export const getBlog = (
  { page = 1, category = "all" } = {},
  options = {}
) => {
  const params = {
    page,
    ...(category !== "all" && { category }),
  };

  return fetcher(buildUrl(`/blog`, params), {
    next: { tags: ["blog"] },
    ...options,
  });
};

// SINGLE
export const getBlogPost = (slug, options = {}) =>
  fetcher(buildUrl(`/blog/${slug}`), {
    next: { tags: [`post-${slug}`] },
    ...options,
  });

/* =========================
   📂 CATEGORIES
========================= */
export const getCategories = (options = {}) =>
  fetcher(buildUrl(`/categories`), {
    next: { tags: ["categories"] },
    ...options,
  });