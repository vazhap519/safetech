export default async function getHome() {
  try {
    const res = await fetch("http://127.0.0.1:8000/api/", {
      cache: "no-store",
    });

    if (!res.ok) {
      return null; // 🔥 error-ის ნაცვლად null
    }

    return await res.json();
  } catch (error) {
    console.error("API Error:", error);
    return null; // 🔥 crash აღარ იქნება
  }
}