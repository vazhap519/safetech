import ProjectsPage from "@/app/projects/page";
import SEOCategoryBlock from "@/app/components/SEOCategoryBlock";
import { getBaseUrl } from "@/lib/config";
import { getProjectCategories } from "@/lib/datafetch";
import { getSeoLinks } from "@/lib/getSeoLinks";
import {
  categoryMetadata,
  categorySchemas,
  findCategory,
} from "@/lib/categorySeo";

async function getCategory(slug) {
  const response = await getProjectCategories().catch(() => null);
  return findCategory(response, slug) || { name: slug, slug };
}

function fallbackIntro(name) {
  return `
<p>${name} მიმართულების პროექტებში ნახავთ Safetech-ის მიერ შესრულებულ სამუშაოებს, გამოყენებულ გადაწყვეტილებებს და პრაქტიკულ შედეგებს.</p>
<p>ეს გვერდი ეხმარება მომხმარებელს სწრაფად იპოვოს შესაბამისი გამოცდილება და შეაფასოს, როგორ შეიძლება მსგავსი გადაწყვეტის გამოყენება მის ობიექტზე.</p>
`;
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const category = await getCategory(slug);
  const name = category?.name || slug;
  const url = `${getBaseUrl()}/projects/category/${slug}`;

  return categoryMetadata({
    category,
    fallbackName: name,
    fallbackTitle: (value) => `${value} პროექტები`,
    fallbackDescription: (value) =>
      `${value} მიმართულების შესრულებული პროექტები Safetech-ისგან.`,
    canonical: url,
  });
}

export default async function ProjectCategoryPage({ params }) {
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
      url: `${getBaseUrl()}/projects/category/${slug}`,
      about: {
        "@type": "Thing",
        name,
      },
    },
  });

  return (
    <>
      {schemas.map((schema, index) => (
        <script
          key={`project-category-schema-${index}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
        />
      ))}

      <ProjectsPage searchParams={{ category: slug }} />

      <SEOCategoryBlock
        title={name}
        html={category?.intro_text || fallbackIntro(name)}
        links={keywordMap}
      />
    </>
  );
}
