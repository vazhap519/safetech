import {
  getSitemapIndexPaths,
  sitemapIndex,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  return xmlResponse(
    addSitemapStylesheet(sitemapIndex(await getSitemapIndexPaths())),
  );
}
