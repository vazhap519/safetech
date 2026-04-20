import ServicesPage from "@/app/services/page";
import { getBaseUrl } from "@/lib/config";
import { getSeoLinks } from "@/lib/getSeoLinks";
import { injectInternalLinks } from "@/lib/internalLinks";

async function getCategoryName(slug) {
  try {
    const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL}/service-categories`, {
      next: { revalidate: 300 },
    });

    if (!res.ok) return slug;

    const data = await res.json();
    const categories = Array.isArray(data) ? data : data?.data ?? [];
    const category = categories.find((c) => c.slug === slug);

    return category?.name || slug;
  } catch {
    return slug;
  }
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const name = await getCategoryName(slug);
  const url = `${getBaseUrl()}/services/category/${slug}`;

  return {
    title: `${name} თბილისში | Safetech`,
    description: `${name} სერვისები, ინსტალაცია და ტექნიკური მხარდაჭერა Safetech-ისგან.`,
    alternates: {
      canonical: url,
    },
    robots: {
      index: true,
      follow: true,
    },
    openGraph: {
      title: `${name} თბილისში | Safetech`,
      description: `${name} სერვისები, ინსტალაცია და ტექნიკური მხარდაჭერა Safetech-ისგან.`,
      url,
      type: "website",
      locale: "ka_GE",
      siteName: "Safetech",
    },
  };
}

export default async function CategoryPage({ params }) {
  const { slug } = await params;
  const [keywordMap, name] = await Promise.all([
    getSeoLinks(),
    getCategoryName(slug),
  ]);

  const seoContent = `
<p>${name} არის Safetech-ის ერთ-ერთი ძირითადი მიმართულება ბიზნესისა და კერძო ობიექტებისთვის. მომსახურება მოიცავს დაგეგმვას, ინსტალაციას, გამართვას და შემდგომ ტექნიკურ მხარდაჭერას.</p>
<p>ჩვენ ვმუშაობთ მცირე ოფისებთან, მაღაზიებთან, საცხოვრებელ და კომერციულ ობიექტებთან, სადაც საჭიროა სტაბილური IT ინფრასტრუქტურა, უსაფრთხოება და სწრაფი რეაგირება ტექნიკურ პრობლემებზე.</p>
<p>თუ გჭირდებათ ${name} თბილისში ან საქართველოს სხვა ქალაქში, Safetech დაგეხმარებათ სწორი გადაწყვეტის შერჩევაში, მონტაჟში და სისტემის გამართულ მუშაობაში.</p>
`;

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Service",
            name,
            areaServed: "Georgia",
            provider: {
              "@type": "Organization",
              name: "Safetech",
              url: getBaseUrl(),
            },
          }),
        }}
      />

      <main>
        <ServicesPage searchParams={{ category: slug }} />

        <section className="py-12 bg-gray-50 border-t">
          <div className="max-w-3xl mx-auto px-4 text-center">
            <h2 className="text-xl md:text-2xl font-semibold text-[#0B3C5D] mb-4">
              {name}
            </h2>

            <div
              className="text-gray-600 text-sm leading-relaxed space-y-3"
              dangerouslySetInnerHTML={{
                __html: injectInternalLinks(
                  seoContent,
                  Array.isArray(keywordMap) ? keywordMap : []
                ),
              }}
            />
          </div>
        </section>
      </main>
    </>
  );
}
