import { supportedLocales } from "@/lib/locales";
import { getProjectVideo } from "@/lib/project-video";
import {
  fetchAllPaginated,
  isIndexableProject,
  localizedUrlEntries,
  videoUrlset,
  xmlResponse,
} from "@/lib/sitemap";
import { addSitemapStylesheet } from "@/lib/sitemap-style";

export const dynamic = "force-dynamic";

export async function GET() {
  const entries = await Promise.all(supportedLocales.map(async (locale) => {
    const projects = await fetchAllPaginated("/projects", { locale });
    return projects.filter(isIndexableProject).flatMap((project) => {
      const video = getProjectVideo(project, locale);
      if (!video) return [];
      const entry = localizedUrlEntries(video.path, {}, [locale])[0];
      return [{ loc: entry.loc, video }];
    });
  }));

  return xmlResponse(addSitemapStylesheet(videoUrlset(entries.flat())));
}
