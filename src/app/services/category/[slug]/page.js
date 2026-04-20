import ServicesPage from "@/app/services/page";
import SEOCategoryBlock from "@/app/components/SEOCategoryBlock";
import { getBaseUrl } from "@/lib/config";
import { getServiceCategories } from "@/lib/datafetch";
import { getSeoLinks } from "@/lib/getSeoLinks";
import {
  categoryMetadata,
  categorySchemas,
  findCategory,
} from "@/lib/categorySeo";

async function getCategory(slug) {
  const response = await getServiceCategories().catch(() => null);
  return findCategory(response, slug) || { name: slug, slug };
}

function fallbackIntro(name) {
  return `
<p>${name} Safetech-ის ერთ-ერთი ძირითადი მიმართულებაა ბიზნესისა და კერძო ობიექტებისთვის. სერვისი მოიცავს დაგეგმვას, ინსტალაციას, გამართვას და შემდგომ ტექნიკურ მხარდაჭერას.</p>
<p>ვმუშაობთ ოფისებთან, მაღაზიებთან, საცხოვრებელ და კომერციულ ობიექტებთან, სადაც საჭიროა სტაბილური IT ინფრასტრუქტურა, უსაფრთხოება და სწრაფი რეაგირება ტექნიკურ პრობლემებზე.</p>
<p>თუ გჭირდებათ ${name} თბილისში ან საქართველოს სხვა ქალაქში, Safetech დაგეხმარებათ სწორი გადაწყვეტის შერჩევაში, მონტაჟში და სისტემის გამართულ მუშაობაში.</p>
`;
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const category = await getCategory(slug);
  const name = category?.name || slug;
  const url = `${getBaseUrl()}/services/category/${slug}`;

  return categoryMetadata({
    category,
    fallbackName: name,
    fallbackTitle: (value) => `${value} თბილისში`,
    fallbackDescription: (value) =>
      `${value} სერვისები, ინსტალაცია და ტექნიკური მხარდაჭერა Safetech-ისგან.`,
    canonical: url,
  });
}

export default async function CategoryPage({ params }) {
  const { slug } = await params;
  const [keywordMap, category] = await Promise.all([
    getSeoLinks(),
    getCategory(slug),
  ]);
  const name = category?.name || slug;
  const schemas = categorySchemas({
    category,
    fallbackSchema: {
      "@context": "https://schema.org",
      "@type": "Service",
      name,
      areaServed: "Georgia",
      provider: {
        "@type": "Organization",
        name: "Safetech",
        url: getBaseUrl(),
      },
    },
  });

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
        title={name}
        html={category?.intro_text || fallbackIntro(name)}
        links={keywordMap}
      />
    </>
  );
}
