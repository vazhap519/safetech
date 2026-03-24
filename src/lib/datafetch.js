// // export async function getHome() {
// //   try {
// //     const res = await fetch("http://127.0.0.1:8000/api/", {
// //       cache: "no-store",
// //     });

// //     if (!res.ok) return null;

// //     return await res.json();
// //   } catch (error) {
// //     console.error("API Error:", error);
// //     return null;
// //   }
// // }

// // export async function getServices() {
// //   try {
// //     const res = await fetch("http://127.0.0.1:8000/api/services", {
// //       cache: "no-store",
// //     });

// //     if (!res.ok) return null;

// //     return await res.json();
// //   } catch (error) {
// //     console.error("API Error:", error);
// //     return null;
// //   }
// // }
// const API = process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

// async function fetcher(url, options = {}) {
//   try {
//     const res = await fetch(url, {
//       next: { revalidate: 60 }, // 🔥 ISR (სუპერ მნიშვნელოვანია)
//       ...options,
//     });

//     if (!res.ok) {
//       console.error("Fetch error:", res.status);
//       return null;
//     }

//     return await res.json();
//   } catch (error) {
//     console.error("API Error:", error);
//     return null;
//   }
// }
// // 🔵 Home
// export const getHome = () => fetcher(`${API}/`);

// // 🟢 ყველა სერვისი
// export const getServices = () => fetcher(`${API}/services`);

// // 🔥 ერთი სერვისი (FIXED URL)
// export const getService = (slug) =>
//   fetcher(`${API}/services/${slug}`);



const API =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000/api";

/* =========================
   FETCHER
========================= */
async function fetcher(url, options = {}) {
  try {
    const res = await fetch(url, {
      ...options, // 🔥 აქ გადმოვა revalidate / cache
    });

    if (!res.ok) {
      console.error("Fetch error:", res.status);
      return null;
    }

    return await res.json();
  } catch (error) {
    console.error("API Error:", error);
    return null;
  }
}

/* =========================
   API CALLS
========================= */

// 🔵 Home
export const getHome = (options = {}) =>
  fetcher(`${API}/`, options);

// 🟢 ყველა სერვისი
export const getServices = (page = 1, options = {}) =>
  fetcher(`${API}/services?page=${page}`, options);

// 🔥 ერთი სერვისი
export const getService = (slug, options = {}) =>
  fetcher(`${API}/services/${slug}`, options);
//სეტინგები
export const getSettings=(options={})=>
  fetcher(`${API}/settings`,options)