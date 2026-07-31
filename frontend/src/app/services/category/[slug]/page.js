import { notFound } from "next/navigation";

import ServicesPageContent from "@/components/pages/ServicesPageContent";
import CategorySeoContent from "@/components/seo/CategorySeoContent";
import {
  createCategoryMetadataGenerator,
  getCategoryPageData,
} from "@/lib/category-data";

export const generateMetadata = createCategoryMetadataGenerator("services");

export default async function CategoryPage({ params }) {
  const { slug } = await params;
  const { category, locale, path } = await getCategoryPageData("services", slug);

  if (!category) notFound();

  return (
    <>
      <ServicesPageContent searchParams={{ category: slug }} showPageSchema={false} />
      <CategorySeoContent category={category} locale={locale} path={path} />
    </>
  );
}
