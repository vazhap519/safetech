import { notFound } from "next/navigation";

import ProjectsPage from "@/app/projects/page";
import CategorySeoContent from "@/components/seo/CategorySeoContent";
import { getCategoryPageData } from "@/lib/category-data";
import { categoryMetadata } from "@/lib/categorySeo";
import { createMetadata } from "@/lib/seo";

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const { category, locale, path } = await getCategoryPageData("projects", slug);

  if (!category) {
    return createMetadata({
      title: "Category not found",
      description: "The requested project category does not exist.",
      path,
      locale,
      noindex: true,
    });
  }

  return categoryMetadata({
    category,
    path,
    locale,
  });
}

export default async function ProjectCategoryPage({ params }) {
  const { slug } = await params;
  const { category } = await getCategoryPageData("projects", slug);

  if (!category) notFound();

  return (
    <>
      <ProjectsPage searchParams={{ category: slug }} />
      <CategorySeoContent category={category} />
    </>
  );
}
