import ProjectList from "@/app/components/projects/ProjectList";
import EmptyState from "../components/ui/EmptyState";

import { getProjects, getEmpty, getSeoByKey } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";

/* =========================
   🔥 SEO (PROJECTS)
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("projects"); // ⚠️ KEY შეცვალე თუ სხვაა
  const data = seo?.data;

  return buildMetadata({
    title: data?.title || "პროექტები",
    description: data?.description || "Safetech პროექტები",
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/projects",
  });
}

/* =========================
   PAGE
========================= */
export default async function ProjectsPage({ searchParams }) {
  const params = await searchParams;

  const page = Number(params?.page || 1);
  const category = params?.category || "all";

  const [res, emptyRes] = await Promise.all([
    getProjects({ page, category }),
    getEmpty().catch(() => null),
  ]);

  const empty = emptyRes?.data || emptyRes || null;

  /* ❌ backend down */
  if (!res || res.error) {
    return <EmptyState empty={empty} />;
  }

  const projects = res?.data ?? [];

  /* 📭 empty data */
  if (projects.length === 0) {
    return <EmptyState empty={empty} />;
  }

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">

        {/* TITLE */}
        <h1 className="text-3xl font-bold text-[#0B3C5D] text-center mb-10">
          პროექტები
        </h1>

        {/* LIST */}
        <ProjectList page={page} category={category} />

      </div>
    </main>
  );
}