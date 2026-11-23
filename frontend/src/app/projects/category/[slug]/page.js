import { notFound } from "next/navigation";

import ProjectsPageContent from "@/components/pages/ProjectsPageContent";
import CategorySeoContent from "@/components/seo/CategorySeoContent";
import {
  createCategoryMetadataGenerator,
  getCategoryPageData,
} from "@/lib/category-data";

export const generateMetadata = createCategoryMetadataGenerator("projects");

export default async function ProjectCategoryPage({ params, searchParams }) {
  const { slug } = await params;
  const { page } = (await searchParams) || {};
  const { category, locale, path } = await getCategoryPageData("projects", slug);

  if (!category) notFound();

  return (
    <>
      <CategorySeoContent category={category} locale={locale} path={path} />
      <ProjectsPageContent
        searchParams={{ category: slug, page }}
        showHero={false}
        showPageSchema={false}
      />
    </>
  );
}
