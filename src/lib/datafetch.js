
const API =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

/* =========================
   🔥 CLEAN FETCHER (FIXED)
========================= */
async function fetcher(url, options = {}) {
  try {
    // ❗ არანაირი cache logic აქ
    const res = await fetch(url, options);

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
   🔵 HOME (ISR)
========================= */
export const getHome = () =>
  fetcher(buildUrl(`/`), {
    next: { revalidate: 60, tags: ["home"] },
  });

/* =========================
   🟢 SERVICES
========================= */

// // 🔥 LIST (DYNAMIC)
// export const getServices = ({ page = 1 } = {}) =>
//   fetcher(buildUrl(`/services`, { page }), {
//     cache: "no-store",
//   });

export const getServices = ({ page = 1, category } = {}) =>
  fetcher(
    buildUrl(`/services`, {
      page,
      ...(category && { category }),
    }),
    {
      cache: "no-store",
    }
  );

// 🔥 SINGLE (ISR)
export const getService = (slug) =>
  fetcher(buildUrl(`/services/${slug}`), {
    next: { revalidate: 300, tags: [`service-${slug}`] },
  });

/* =========================
   ⚙ SETTINGS (ISR)
========================= */
export const getSettings = () =>
  fetcher(buildUrl(`/settings`), {
    next: { revalidate: 300, tags: ["settings"] },
  });

/* =========================
   🔒 PRIVACY (ISR)
========================= */
export const getPrivacy = () =>
  fetcher(buildUrl(`/privacy`), {
    next: { revalidate: 300, tags: ["privacy"] },
  });

/* =========================
   📘 ABOUT (ISR)
========================= */
export const getAbout = () =>
  fetcher(buildUrl(`/about`), {
    next: { revalidate: 300, tags: ["about"] },
  });

/* =========================
   📰 BLOG
========================= */

// 🔥 LIST (DYNAMIC)
export const getBlog = ({ page = 1, category = "all" } = {}) => {
  const params = {
    page,
    ...(category !== "all" && { category }),
  };

  return fetcher(buildUrl(`/blog`, params), {
    cache: "no-store",
  });
};

// 🔥 SINGLE (ISR)
export const getBlogPost = (slug) =>
  fetcher(buildUrl(`/blog/${slug}`), {
    next: { revalidate: 300, tags: [`post-${slug}`] },
  });

/* =========================
   📂 CATEGORIES (ISR)
========================= */
export const getCategories = () =>
  fetcher(buildUrl(`/categories`), {
    next: { revalidate: 300, tags: ["categories"] },
  });

/* =========================
   📂 SEO (ISR)
========================= */

// ყველა SEO
export const getSeo = () =>
  fetcher(buildUrl(`/seo`), {
    next: { revalidate: 300, tags: ["seo"] },
  });

// კონკრეტული გვერდის SEO
export const getSeoByKey = (key) =>
  fetcher(buildUrl(`/seo/${key}`), {
    next: { revalidate: 300, tags: [`seo-${key}`] },
  });

/* =========================
   📩 CONTACT
========================= */

// POST (always dynamic)
export const sendContact = (payload) =>
  fetcher(buildUrl(`/contact`), {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
    cache: "no-store",
  });

// GET (ISR)
export const getContact = () =>
  fetcher(buildUrl(`/contact-page`), {
    next: { revalidate: 300, tags: ["contact-page"] },
  });