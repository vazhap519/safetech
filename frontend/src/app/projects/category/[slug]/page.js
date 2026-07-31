import { notFound } from "next/navigation";

import ProjectsPageContent from "@/components/pages/ProjectsPageContent";
import CategorySeoContent from "@/components/seo/CategorySeoContent";
import {
  createCategoryMetadataGenerator,
  getCategoryPageData,
} from "@/lib/category-data";

export const generateMetadata = createCategoryMetadataGenerator("projects");

export default async function ProjectCategoryPage({ params }) {
  const { slug } = await params;
  const { category, locale, path } = await getCategoryPageData("projects", slug);

  if (!category) notFound();

  return (
    <>
      <ProjectsPageContent searchParams={{ category: slug }} showPageSchema={false} />
      <CategorySeoContent category={category} locale={locale} path={path} />
    </>
  );
}
