import Link from "next/link";
import Image from "next/image";
import { servicesList } from "@/data/services";
import { buildMetadata } from "@/lib/seo";

/* =========================
   META SEO
========================= */
export const metadata = buildMetadata({
  title: "IT სერვისები საქართველოში",
  description:
    "Safetech გთავაზობთ კამერების მონტაჟს, POS სისტემებს და IT სერვისებს.",
  path: "/services",
});

/* =========================
   PAGE
========================= */
export default function ServicesPage() {
  return (
    <main>

      {/* JSON-LD (ItemList) */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "ItemList",
            itemListElement: servicesList.map((service, index) => ({
              "@type": "ListItem",
              position: index + 1,
              name: service.title,
              url: `https://safetech.ge/services/${service.slug}`,
            })),
          }),
        }}
      />

      {/* HERO */}
      <section className="py-20 bg-[#0B3C5D] text-white text-center">
        <h1 className="text-4xl md:text-5xl font-bold">
          ჩვენი სერვისები
        </h1>

        <p className="mt-4 text-gray-300 max-w-xl mx-auto">
          აირჩიე შენთვის საჭირო IT და უსაფრთხოების სერვისი და მიიღე პროფესიონალური დახმარება
        </p>
      </section>

      {/* SERVICES GRID */}
      <section className="py-20 bg-[#F8FAFC]">
        <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

          {servicesList.map((service) => (
            <Link key={service.slug} href={`/services/${service.slug}`}>
              <div
                className="group bg-white rounded-2xl overflow-hidden shadow-sm 
                hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] 
                transition-all duration-300 cursor-pointer"
              >

                {/* Image */}
                <div className="relative overflow-hidden">
                  <Image
                    src={service.image}
                    alt={service.title}
                    width={500}
                    height={300}
                    className="w-full h-44 object-cover group-hover:scale-110 transition duration-300"
                  />

                  <div className="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition"></div>
                </div>

                {/* Content */}
                <div className="p-6">
                  <h3 className="font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                    {service.title}
                  </h3>

                  <p className="text-sm text-gray-600 mt-2 leading-relaxed">
                    {service.desc}
                  </p>

                  <div className="mt-4 text-sm text-[#00C2A8] font-medium">
                    დეტალურად →
                  </div>
                </div>

              </div>
            </Link>
          ))}

        </div>
      </section>

    </main>
  );
}