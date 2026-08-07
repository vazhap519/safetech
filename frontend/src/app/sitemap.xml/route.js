import { sitemapIndex, xmlResponse } from "@/lib/sitemap";
import { SITEMAP_INDEX_PATHS } from "@/lib/sitemap-index";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  return xmlResponse(
    addSitemapStylesheet(sitemapIndex(SITEMAP_INDEX_PATHS)),
  );
}
