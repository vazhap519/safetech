const API =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

const isDev = process.env.NODE_ENV === "development";

/* =========================
   🔥 FIXED FETCHER (PROPER)
========================= */
async function fetcher(url, options = {}) {
  try {
    const finalOptions = {
      ...options,
    };

    // ✅ DEV → no cache საერთოდ
    if (isDev) {
      finalOptions.cache = "no-store";
    } else {
      // ✅ PROD → მხოლოდ ის გამოიყენე რაც გადმოგცეს
      if (options.next) {
        finalOptions.next = options.next;
      } else {
        finalOptions.cache = "force-cache";
      }
    }

    const res = await fetch(url, finalOptions);

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
   🔵 HOME
========================= */
export const getHome = () =>
  fetcher(buildUrl(`/`), {
    next: { tags: ["home"] },
  });

/* =========================
   🟢 SERVICES
========================= */

// LIST
export const getServices = ({ page = 1 } = {}) =>
  fetcher(buildUrl(`/services`, { page }), {
    next: { tags: [`services-page-${page}`] }, // 🔥 FIXED
  });

export const getService = (slug) =>
  fetcher(buildUrl(`/services/${slug}`), {
    next: { tags: [`service-${slug}`] },
  });
/* =========================
   ⚙ SETTINGS
========================= */
export const getSettings = () =>
  fetcher(buildUrl(`/settings`), {
    next: { tags: ["settings"] },
  });
/* =========================
   🔒 PRIVACY
========================= */
export const getPrivacy = () =>
  fetcher(buildUrl(`/privacy`), {
    next: { tags: ["privacy"] },
  });
/* =========================
   📘 ABOUT
========================= */
export const getAbout = () =>
  fetcher(buildUrl(`/about`), {
    next: { tags: ["about"] },
  });
/* =========================
   📰 BLOG
========================= */

export const getBlog = ({ page = 1, category = "all" } = {}) => {
  const params = {
    page,
    ...(category !== "all" && { category }),
  };

  return fetcher(buildUrl(`/blog`, params), {
    next: { tags: [`blog-${category}-page-${page}`] }, // 🔥 FIXED
  });
};

export const getBlogPost = (slug) =>
  fetcher(buildUrl(`/blog/${slug}`), {
    next: { tags: [`post-${slug}`] },
  });
/* =========================
   📂 CATEGORIES
========================= */
export const getCategories = () =>
  fetcher(buildUrl(`/categories`), {
    next: { tags: ["categories"] },
  });

 /* =========================
   📂 Seo
========================= */

// ყველა SEO (sitemap / admin)
export const getSeo = () =>
  fetcher(buildUrl(`/seo`), {
    next: { tags: ["seo"] },
  });

// კონკრეტული გვერდის SEO (🔥 მთავარი)
export const getSeoByKey = (key) =>
  fetcher(buildUrl(`/seo/${key}`), {
    next: { tags: [`seo-${key}`] },
  });
  /* =========================
   📩 CONTACT
========================= */
export const sendContact = (payload) =>
  fetcher(buildUrl(`/contact`), {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),

    // ❗ POST-ზე cache არ გვინდა
    cache: "no-store",
  });