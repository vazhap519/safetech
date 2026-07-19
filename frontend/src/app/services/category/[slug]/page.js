import { notFound } from "next/navigation";

import ServicesPage from "@/app/services/page";
import CategorySeoContent from "@/components/seo/CategorySeoContent";
import { getCategoryPageData } from "@/lib/category-data";
import { categoryMetadata } from "@/lib/categorySeo";
import { createMetadata } from "@/lib/seo";

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const { category, locale, path } = await getCategoryPageData("services", slug);

  if (!category) {
    return createMetadata({
      title: "Category not found",
      description: "The requested service category does not exist.",
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

export default async function CategoryPage({ params }) {
  const { slug } = await params;
  const { category } = await getCategoryPageData("services", slug);

  if (!category) notFound();

  return (
    <>
      <ServicesPage searchParams={{ category: slug }} />
      <CategorySeoContent category={category} />
    </>
  );
}
