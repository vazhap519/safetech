import Link from "next/link";
import Image from "next/image";
import { buildMetadata } from "@/lib/seo";
import { getServices } from "@/lib/datafetch";

/* =========================
   META SEO
========================= */
export async function generateMetadata() {
  return buildMetadata({
    title: "IT სერვისები საქართველოში",
    description:
      "Safetech გთავაზობთ კამერების მონტაჟს, POS სისტემებს და IT სერვისებს.",
    path: "/services",
  });
}

/* =========================
   PAGE
========================= */
export default async function ServicesPage({ searchParams }) {

  const params = searchParams;
  const page = Number(params?.page || 1);

  const data = await getServices({ page });

  // ✅ GLOBAL SAFETY
  if (!data || data.error) {
    return (
      <div className="text-center py-20 text-red-500">
        სერვისები ვერ ჩაიტვირთა 😔
      </div>
    );
  }

  const services = Array.isArray(data?.services)
    ? data.services
    : [];

  const meta = data?.meta || {};
  const hero = data?.serviceHero;

  const totalPages = meta?.last_page || 1;

  return (
    <main>

      {/* HERO */}
      <section className="py-20 bg-[#0B3C5D] text-white text-center">
        <h1 className="text-4xl md:text-5xl font-bold">
          {hero?.title || "ჩვენი სერვისები"}
        </h1>

        <p className="mt-4 text-gray-300 max-w-xl mx-auto">
          {hero?.description || "აირჩიე შენთვის საჭირო IT სერვისი"}
        </p>
      </section>

      {/* GRID */}
      <section className="py-20 bg-[#F8FAFC]">
        <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

          {services.map((service) => (
            <Link key={service.slug} href={`/services/${service.slug}`}>
              <div className="bg-white rounded-2xl shadow hover:shadow-lg transition">

                <Image
                  src={service.image || "/placeholder.jpg"}
                  alt={service.title}
                  width={500}
                  height={300}
                  className="w-full h-44 object-cover rounded-t-2xl"
                />

                <div className="p-5">
                  <h3 className="font-semibold text-[#0B3C5D]">
                    {service.title}
                  </h3>

                  <p className="text-sm text-gray-600 mt-2">
                    {service.description}
                  </p>
                </div>

              </div>
            </Link>
          ))}

        </div>

        {/* PAGINATION */}
        <div className="flex justify-center mt-10 gap-2">

          {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
            <Link
              key={p}
              href={`/services?page=${p}`}
              className={`px-4 py-2 rounded ${
                p === page
                  ? "bg-[#00C2A8] text-white"
                  : "bg-gray-200"
              }`}
            >
              {p}
            </Link>
          ))}

        </div>

      </section>

    </main>
  );
}