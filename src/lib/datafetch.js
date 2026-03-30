// const API =
//   process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

// const isDev = process.env.NODE_ENV === "development";

// /* =========================
//    🔥 FIXED FETCHER (PROPER)
// ========================= */
// async function fetcher(url, options = {}) {
//   try {
//     const finalOptions = {
//       ...options,
//     };

//     // ✅ DEV → no cache საერთოდ
// if (isDev) {
//   finalOptions.cache = "no-store";
// } else {
//   if (options.next) {
//     finalOptions.next = options.next; // ✅ cache + tags
//   } else if (!options.cache) {
//     finalOptions.cache = "force-cache"; // ✅ default
//   }
// }

//     const res = await fetch(url, finalOptions);

//     const text = await res.text();

//     let data = null;
//     try {
//       data = text ? JSON.parse(text) : null;
//     } catch (e) {
//       console.warn("⚠️ JSON parse failed:", text);
//     }

//     if (!res.ok) {
//       console.error("❌ API ERROR:", {
//         url,
//         status: res.status,
//         body: text,
//       });

//       return {
//         error: true,
//         status: res.status,
//         data,
//       };
//     }

//     return data;
//   } catch (error) {
//     console.error("❌ FETCH FAILED:", error);

//     return {
//       error: true,
//       message: error.message,
//     };
//   }
// }

// /* =========================
//    🔧 QUERY BUILDER
// ========================= */
// function buildUrl(path, params = {}) {
//   const query = new URLSearchParams(params).toString();
//   return `${API}${path}${query ? `?${query}` : ""}`;
// }

// /* =========================
//    🔵 HOME
// ========================= */
// export const getHome = () =>
//   fetcher(buildUrl(`/`), {
//     next: { tags: ["home"] },
//   });

// /* =========================
//    🟢 SERVICES
// ========================= */

// // LIST
// export const getServices = ({ page = 1 } = {}) =>
//   fetcher(buildUrl(`/services`, { page }), {
//     cache: "no-store", // 🔥 override
//   });

// export const getService = (slug) =>
//   fetcher(buildUrl(`/services/${slug}`), {
//     next: { tags: [`service-${slug}`] },
//   });
// /* =========================
//    ⚙ SETTINGS
// ========================= */
// export const getSettings = () =>
//   fetcher(buildUrl(`/settings`), {
//     next: { tags: ["settings"] },
//   });
// /* =========================
//    🔒 PRIVACY
// ========================= */
// export const getPrivacy = () =>
//   fetcher(buildUrl(`/privacy`), {
//     next: { tags: ["privacy"] },
//   });
// /* =========================
//    📘 ABOUT
// ========================= */
// export const getAbout = () =>
//   fetcher(buildUrl(`/about`), {
//     next: { tags: ["about"] },
//   });
// /* =========================
//    📰 BLOG
// ========================= */

// export const getBlog = ({ page = 1, category = "all" } = {}) => {
//   const params = {
//     page,
//     ...(category !== "all" && { category }),
//   };

//   return fetcher(buildUrl(`/blog`, params), {
//     next: { tags: [`blog-${category}-page-${page}`] }, // 🔥 FIXED
//   });
// };

// export const getBlogPost = (slug) =>
//   fetcher(buildUrl(`/blog/${slug}`), {
//     next: { tags: [`post-${slug}`] },
//   });
// /* =========================
//    📂 CATEGORIES
// ========================= */
// export const getCategories = () =>
//   fetcher(buildUrl(`/categories`), {
//     next: { tags: ["categories"] },
//   });

//  /* =========================
//    📂 Seo
// ========================= */

// // ყველა SEO (sitemap / admin)
// export const getSeo = () =>
//   fetcher(buildUrl(`/seo`), {
//     next: { tags: ["seo"] },
//   });

// // კონკრეტული გვერდის SEO (🔥 მთავარი)
// export const getSeoByKey = (key) =>
//   fetcher(buildUrl(`/seo/${key}`), {
//     next: { tags: [`seo-${key}`] },
//   });
//   /* =========================
//    📩 CONTACT
// ========================= */
// export const sendContact = (payload) =>
//   fetcher(buildUrl(`/contact`), {
//     method: "POST",
//     headers: {
//       "Content-Type": "application/json",
//     },
//     body: JSON.stringify(payload),

//     // ❗ POST-ზე cache არ გვინდა
//     cache: "no-store",
//   });

//   export const getContact = () =>
//   fetcher(buildUrl(`/contact-page`), {
//     next: { tags: ["contact-page"] },
//   });


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

// 🔥 LIST (DYNAMIC)
export const getServices = ({ page = 1 } = {}) =>
  fetcher(buildUrl(`/services`, { page }), {
    cache: "no-store",
  });

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