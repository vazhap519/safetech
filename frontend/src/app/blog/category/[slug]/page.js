import BlogPage from "@/app/blog/page";
import { notFound } from "next/navigation";

import CategorySeoContent from "@/components/seo/CategorySeoContent";
import { getCategoryPageData } from "@/lib/category-data";
import { categoryMetadata } from "@/lib/categorySeo";
import { createMetadata } from "@/lib/seo";

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const { category, locale, path } = await getCategoryPageData("blog", slug);

  if (!category) {
    return createMetadata({
      title: "Category not found",
      description: "The requested blog category does not exist.",
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

export default async function BlogCategoryPage({ params }) {
  const { slug } = await params;
  const { category } = await getCategoryPageData("blog", slug);

  if (!category) notFound();

  return (
    <>
      <BlogPage
        heading={category.name}
        searchParams={{ category: slug }}
        showPageSchema={false}
      />
      <CategorySeoContent category={category} />
    </>
  );
}
