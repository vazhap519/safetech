import ServicesPage from "@/app/services/page";
import { injectInternalLinks } from "@/lib/internalLinks";
import { getSeoLinks } from "@/lib/getSeoLinks";

/* =========================
   🔥 FETCH CATEGORY NAME
========================= */
async function getCategoryName(slug) {
  try {
    const res = await fetch(
      `${process.env.NEXT_PUBLIC_API_URL}/service-categories`,
      { cache: "no-store" }
    );

    const data = await res.json();

    const category = data.find((c) => c.slug === slug);

    return category?.name || slug;
  } catch {
    return slug;
  }
}

/* =========================
   🔥 SEO
========================= */
export async function generateMetadata({ params }) {
  const { slug } = params;


const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
  const name = await getCategoryName(slug);

  const url = `${BASE_URL}/services/category/${slug}`;

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
  };
}

/* =========================
   PAGE
========================= */
export default async function CategoryPage({ params }) {
  const { slug } = params;

  const keywordMap = await getSeoLinks();
  const name = await getCategoryName(slug);

  /* 🔥 SEO CONTENT (NATURAL) */
  const seoContent = `
${name} წარმოადგენს თანამედროვე IT მომსახურების ერთ-ერთ ყველაზე მნიშვნელოვან მიმართულებას, რომელიც უზრუნველყოფს ბიზნესის გამართულ და უსაფრთხო მუშაობას.

ჩვენ გთავაზობთ ${name}-ის სრულ მომსახურებას — ინსტალაციიდან ტექნიკურ მხარდაჭერამდე. ჩვენი გუნდი მუშაობს როგორც მცირე, ასევე დიდი ბიზნეს ინფრასტრუქტურასთან.

თუ გჭირდებათ ${name} თბილისში, Safetech უზრუნველყოფს სწრაფ, ხარისხიან და პროფესიონალურ მომსახურებას.
`;

  return (
    <>
      {/* 🔥 SCHEMA */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "Service",
            name: name,
            areaServed: "Tbilisi",
          }),
        }}
      />

      <main>

        {/* 🔥 SERVICES */}
        <ServicesPage searchParams={{ category: slug }} />

        {/* 🔥 CLEAN SEO BLOCK */}
        <section className="py-12 bg-gray-50 border-t">
          <div className="max-w-3xl mx-auto px-4 text-center">

            <h2 className="text-xl md:text-2xl font-semibold text-[#0B3C5D] mb-4">
              {name}
            </h2>

            <div
              className="text-gray-600 text-sm leading-relaxed space-y-3"
              dangerouslySetInnerHTML={{
                __html: injectInternalLinks(seoContent, keywordMap),
              }}
            />

          </div>
        </section>

      </main>
    </>
  );
}