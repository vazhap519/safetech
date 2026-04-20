import BlogPage from "@/app/blog/page";
import SEOCategoryBlock from "@/app/components/SEOCategoryBlock";
import { getBaseUrl } from "@/lib/config";
import { getCategories } from "@/lib/datafetch";
import { getSeoLinks } from "@/lib/getSeoLinks";
import {
  categoryMetadata,
  categorySchemas,
  findCategory,
} from "@/lib/categorySeo";

async function getCategory(slug) {
  const response = await getCategories().catch(() => null);
  return findCategory(response, slug) || { name: slug, slug };
}

function fallbackIntro(name) {
  return `
<p>${name} თემაზე Safetech-ის ბლოგში ნახავთ პრაქტიკულ რჩევებს, ტექნიკურ ახსნებს და ბიზნესისთვის გამოსადეგ რეკომენდაციებს.</p>
<p>სტატიები გეხმარებათ სწორად დაგეგმოთ IT ინფრასტრუქტურა, უსაფრთხოების სისტემები და ყოველდღიური ტექნიკური მხარდაჭერა.</p>
`;
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const category = await getCategory(slug);
  const name = category?.name || slug;
  const url = `${getBaseUrl()}/blog/category/${slug}`;

  return categoryMetadata({
    category,
    fallbackName: name,
    fallbackTitle: (value) => `${value} ბლოგი`,
    fallbackDescription: (value) =>
      `${value} თემაზე Safetech-ის სტატიები, რჩევები და პრაქტიკული ინფორმაცია.`,
    canonical: url,
  });
}

export default async function BlogCategoryPage({ params }) {
  const { slug } = await params;
  const [category, keywordMap] = await Promise.all([
    getCategory(slug),
    getSeoLinks(),
  ]);
  const name = category?.name || slug;
  const schemas = categorySchemas({
    category,
    fallbackSchema: {
      "@context": "https://schema.org",
      "@type": "CollectionPage",
      name,
      url: `${getBaseUrl()}/blog/category/${slug}`,
      isPartOf: {
        "@type": "Blog",
        name: "Safetech Blog",
      },
    },
  });

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
        title={name}
        html={category?.intro_text || fallbackIntro(name)}
        links={keywordMap}
      />
    </>
  );
}
