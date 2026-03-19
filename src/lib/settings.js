export async function getSettings() {
  try {
    const res = await fetch("http://localhost:8000/api/settings", {
      cache: "no-store",
    });

    if (!res.ok) {
      throw new Error("Failed to fetch settings");
    }

    return await res.json();
  } catch (error) {
    console.error("Settings fetch error:", error);

    // fallback (optional)
    return {
      titles: {},
      navigation: {},
      styles: {},
      logo: null,
    };
  }
}
// export async function getHero() {
//   const res = await fetch("http://127.0.0.1:8000/api/home", {
//     cache: "no-store"
//   });

//   if (!res.ok) {
//     throw new Error("Failed to fetch hero");
//   }

//   const data = await res.json();

//   return data.data.hero;
// }

// export async function getCta() {
//   const res = await fetch("http://127.0.0.1:8000/api/home", {
//     cache: "no-store"
//   });

//   const data = await res.json();

//   return data.data;
// }

export async function getHomeData() {
  const res = await fetch("http://127.0.0.1:8000/api/home", {
    cache: "no-store"
  });

  if (!res.ok) {
    throw new Error("Failed to fetch home data");
  }

  const data = await res.json();

  return data.data;
}