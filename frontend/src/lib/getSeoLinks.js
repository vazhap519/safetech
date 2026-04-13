export async function getSeoLinks() {
  try {
    const res = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/seo-links`,
      {
        next: { revalidate: 300 },
      }
    );

    if (!res.ok) return [];

    return await res.json();
  } catch (e) {
    console.warn("SEO LINKS ERROR", e);
    return [];
  }
}