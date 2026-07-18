import BlogPage from "@/app/blog/page";
import SEOCategoryBlock from "@/app/components/SEOCategoryBlock";
import {
  categoryMetadata,
  categorySchemas,
  findCategory,
} from "@/lib/categorySeo";
import { getBaseUrl } from "@/lib/config";
import { getCategories } from "@/lib/datafetch";
import { getSeoLinks } from "@/lib/getSeoLinks";

async function getCategory(slug) {
  const response = await getCategories().catch(() => null);
  return findCategory(response, slug) || { name: slug, slug };
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const category = await getCategory(slug);
  const url = `${getBaseUrl()}/blog/category/${slug}`;

  return categoryMetadata({
    category,
    canonical: url,
  });
}

export default async function BlogCategoryPage({ params }) {
  const { slug } = await params;
  const [category, keywordMap] = await Promise.all([
    getCategory(slug),
    getSeoLinks(),
  ]);
  const schemas = categorySchemas({ category });

  return (
    <>
      {schemas.map((schema, index) => (
        <script
          key={`blog-category-schema-${index}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
        />
      ))}

      <BlogPage searchParams={{ category: slug }} />

      <SEOCategoryBlock
        title={category?.name || ""}
        html={category?.intro_text || ""}
        links={keywordMap}
      />
    </>
  );
}
