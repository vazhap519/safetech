import ProjectList from "@/app/components/projects/ProjectList";
import EmptyState from "../components/ui/EmptyState";

import { getBaseUrl } from "@/lib/config";
import { getProjects, getEmpty, getProjectCategories, getSeoByKey } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";

/* =========================
   🔥 SEO (PROJECTS)
========================= */
export async function generateMetadata({ searchParams }) {
  const params = await searchParams;
  const page = Number(params?.page || 1);
  const category = params?.category || "all";
  const seo = await getSeoByKey("projects");
  const data = seo?.data;
  const categoryPath =
    category && category !== "all" ? `/projects/category/${category}` : "/projects";
  const canonical =
    page > 1
      ? `${getBaseUrl()}${categoryPath}/page/${page}`
      : `${getBaseUrl()}${categoryPath}`;

  return buildMetadata({
    title: data?.title || "პროექტები",
    description: data?.description || "Safetech პროექტები",
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical,
    noindex: page > 5 || data?.noindex,
    og: data?.og,
    path: "/projects",
  });
}

/* =========================
   PAGE
========================= */
export default async function ProjectsPage({ searchParams }) {
  const params = await searchParams;

  const page = Number(params?.page || 1);
  const category = params?.category || "all";

  const [res, categoriesRes, emptyRes] = await Promise.all([
    getProjects({ page, category }),
    getProjectCategories().catch(() => null),
    getEmpty().catch(() => null),
  ]);

  const empty = emptyRes?.data || emptyRes || null;

  /* ❌ backend down */
  if (!res || res.error) {
    return <EmptyState empty={empty} />;
  }

  const projects = res?.data ?? [];
  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data || [];
  const meta = res?.meta || {};

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
        <ProjectList
          projects={projects}
          categories={categories}
          meta={meta}
          page={page}
          category={category}
        />

      </div>
    </main>
  );
}
