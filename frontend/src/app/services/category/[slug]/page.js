import SEOCategoryBlock from "@/app/components/SEOCategoryBlock";
import ServicesPage from "@/app/services/page";
import {
  categoryMetadata,
  categorySchemas,
  findCategory,
} from "@/lib/categorySeo";
import { getBaseUrl } from "@/lib/config";
import { getSeoLinks } from "@/lib/getSeoLinks";
import { getServiceCategories } from "@/lib/datafetch";

async function getCategory(slug) {
  const response = await getServiceCategories().catch(() => null);
  return findCategory(response, slug) || { name: slug, slug };
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const category = await getCategory(slug);
  const url = `${getBaseUrl()}/services/category/${slug}`;

  return categoryMetadata({
    category,
    canonical: url,
  });
}

export default async function CategoryPage({ params }) {
  const { slug } = await params;
  const [keywordMap, category] = await Promise.all([
    getSeoLinks(),
    getCategory(slug),
  ]);
  const schemas = categorySchemas({ category });

  return (
    <>
      {schemas.map((schema, index) => (
        <script
          key={`service-category-schema-${index}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
        />
      ))}

      <ServicesPage searchParams={{ category: slug }} />

      <SEOCategoryBlock
        title={category?.name || ""}
        html={category?.intro_text || ""}
        links={keywordMap}
      />
    </>
  );
}
