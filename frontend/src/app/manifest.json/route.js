export const revalidate = 300;

const fallbackManifest = {
  name: "Safetech",
  short_name: "Safetech",
  start_url: "/",
  display: "standalone",
  background_color: "#ffffff",
  theme_color: "#0B3C5D",
  icons: [],
};

export async function GET() {
  try {
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/manifest.json`, {
      next: { revalidate: 300 },
    });

    if (res.ok) {
      const manifest = await res.json();
      return Response.json(manifest);
    }
  } catch {
    //
  }

  return Response.json(fallbackManifest);
}
