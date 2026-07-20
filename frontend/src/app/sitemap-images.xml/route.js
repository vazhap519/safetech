import { fetchImageSitemapItems, imageUrlset, xmlResponse } from "@/lib/sitemap";

export const dynamic = "force-dynamic";

export async function GET() {
  return xmlResponse(imageUrlset(await fetchImageSitemapItems()));
}
