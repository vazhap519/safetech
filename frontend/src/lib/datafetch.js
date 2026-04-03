
const API =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";


async function fetcher(url, options = {}) {
  try {
    const res = await fetch(url, options);

    const text = await res.text();

    let data = null;
    try {
      data = text ? JSON.parse(text) : null;
    } catch (e) {
      console.warn("⚠️ JSON parse failed:", text);
    }

    if (!res.ok) {
      console.warn("❌ API ERROR:", url);

      return {
        error: true,
        data: null,
      };
    }

    return data;
  } catch (error) {
    console.warn("❌ FETCH FAILED:", url);

    /* 🔥 აქ არის მთავარი */
    return {
      error: true,
      offline: true, // ✅ ვიგებთ რომ backend მკვდარია
      data: null,
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



export const getServices = ({ page = 1, category } = {}) =>
  fetcher(
    buildUrl(`/services`, {
      page,
      ...(category && { category }),
    }),
    {
      next: { revalidate: 30, tags: ["services"] },
    }
  );

// 🔥 SINGLE (ISR)
export const getService = (slug) =>
  fetcher(buildUrl(`/services/${slug}`), {
    next: { revalidate: 30, tags: [`service-${slug}`] },
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
/* =========================
   📂 CATEGORIES (ISR)
========================= */
export const getCategories = () =>
  fetcher(buildUrl(`/categories`), {
    next: { revalidate: 300, tags: ["categories"] },
  });
// 🔥 SINGLE (ISR)
export const getBlogPost = (slug) =>
  fetcher(buildUrl(`/blog/${slug}`), {
    next: { revalidate: 300, tags: [`post-${slug}`] },
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
  //Empty
export const getEmpty = async () => {
  try {
    const res = await fetch(buildUrl(`/empty`), {
      cache: "no-store",
    });

    if (!res.ok) {
      console.error("API ERROR:", res.status);
      return null;
    }

    return await res.json();
  } catch (error) {
    console.error("FETCH ERROR:", error);
    return null; // 👈 ყველაზე მნიშვნელოვანი
  }
};
export const getProjects = ({ page = 1, category = "all" }) =>
  fetcher(buildUrl(`/projects?page=${page}&category=${category}`));
export const getProject = (slug) =>
  fetcher(buildUrl(`/projects/${slug}`), {
    next: { revalidate: 300, tags: ["projects"] },
  });
export const getProjectCategories  = () =>
  fetcher(buildUrl(`/project-categories`), {
    next: { revalidate: 300, tags: ["projects"] },
  });
  export const getRelatedProjects = (slug) =>
  fetcher(buildUrl(`/projects/${slug}/related`), {
    next: { revalidate: 300, tags: ["projects"] },
  });