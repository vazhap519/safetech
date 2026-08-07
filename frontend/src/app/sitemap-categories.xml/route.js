import { sitemapIndex, xmlResponse } from "@/lib/sitemap";
import { SITEMAP_INDEX_PATHS } from "@/lib/sitemap-index";

export const dynamic = "force-dynamic";

export async function GET() {
  return xmlResponse(sitemapIndex(SITEMAP_INDEX_PATHS));
}
