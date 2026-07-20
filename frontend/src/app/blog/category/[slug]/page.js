import BlogPage from "@/app/blog/page";
import { notFound } from "next/navigation";

import CategorySeoContent from "@/components/seo/CategorySeoContent";
import {
  createCategoryMetadataGenerator,
  getCategoryPageData,
} from "@/lib/category-data";

export const generateMetadata = createCategoryMetadataGenerator("blog");

export default async function BlogCategoryPage({ params }) {
  const { slug } = await params;
  const { category, locale, path } = await getCategoryPageData("blog", slug);

  if (!category) notFound();

  return (
    <>
      <BlogPage
        heading={category.name}
        searchParams={{ category: slug }}
        showPageSchema={false}
      />
      <CategorySeoContent category={category} locale={locale} path={path} />
    </>
  );
}
