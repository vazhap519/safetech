import { notFound } from "next/navigation";

import ServicesPage from "@/app/services/page";
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
      <ServicesPage searchParams={{ category: slug }} showPageSchema={false} />
      <CategorySeoContent category={category} locale={locale} path={path} />
    </>
  );
}
