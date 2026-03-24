const API =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

/* =========================
   FETCHER (SMART)
========================= */
async function fetcher(url, options = {}) {
  try {
    const res = await fetch(url, {
      next: { revalidate: 60 }, // default cache
      ...options,
    });

    if (!res.ok) {
      console.error("❌ Fetch error:", res.status, url);
      return null;
    }

    return await res.json();
  } catch (error) {
    console.error("❌ API Error:", error);
    return null;
  }
}

/* =========================
   API CALLS (WITH TAGS)
========================= */

// 🔵 HOME
export const getHome = (options = {}) =>
  fetcher(`${API}/`, {
    next: { tags: ["home"] },
    ...options,
  });

// 🟢 SERVICES LIST
export const getServices = (page = 1, options = {}) =>
  fetcher(`${API}/services?page=${page}`, {
    next: { tags: ["services"] },
    ...options,
  });

// 🔥 SINGLE SERVICE
export const getService = (slug, options = {}) =>
  fetcher(`${API}/services/${slug}`, {
    next: { tags: [`service-${slug}`] }, // 🔥 dynamic tag
    ...options,
  });

// ⚙ SETTINGS
export const getSettings = (options = {}) =>
  fetcher(`${API}/settings`, {
    next: { tags: ["settings"] },
    ...options,
  });

// 🔒 PRIVACY
export const getPrivacy = () =>
  fetch(`${API}/privacy`, {
    next: { tags: ["privacy"] },
  });
// 📘 ABOUT
export const getAbout = (options = {}) =>
  fetcher(`${API}/about`, {
    next: { tags: ["about"] },
    ...options,
  });