

import Link from "next/link";
import Image from "next/image";
import { buildMetadata } from "@/lib/seo";
import { getServices, getSeoByKey } from "@/lib/datafetch";
import EmptyState from "../components/ui/EmptyState";
export const dynamic = "force-dynamic";

/* =========================
   SEO (SERVICES 🔥)
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("services");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/services",
  });
}

/* =========================
   PAGE
========================= */
export default async function ServicesPage({ searchParams }) {
  const params = await searchParams;
const page = Number(params?.page || 1);
const category = params?.category || null;

const res = await getServices({ page, category });

  if (!res || res.error) {
    return (
      <div className="text-center py-20 text-red-500">
        სერვისები ვერ ჩაიტვირთა 😔
      </div>
    );
  }

  const data = res.data;
  const seo = res.seo;

  const services = data?.services ?? [];
  const meta = data?.meta ?? {};
  const hero = data?.serviceHero;

  const totalPages = meta?.last_page || 1;
const start = Math.max(1, page - 2);
const end = Math.min(totalPages, page + 2);

const pages = [];
for (let i = start; i <= end; i++) {
  pages.push(i);
}
 if (!hero && services.length === 0) {
  return (
    <main>
      <EmptyState title="სერვისები არ არის დამატებული" />
    </main>
  );
}
  return (
    <>
      {seo?.schema && (
        Array.isArray(seo.schema) ? (
          seo.schema.map((schema, i) => (
            <script key={i} type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }} />
          ))
        ) : (
          <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(seo.schema) }} />
        )
      )}

      <main>

        {hero && (
          <section className="relative py-28 text-white overflow-hidden bg-gradient-to-br from-[#071A2B] via-[#0B3C5D] to-[#0E4F73]">
            <div className="relative text-center max-w-3xl mx-auto px-4">
              <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-3">Safetech</p>
              <h1 className="text-4xl md:text-6xl font-bold">{hero?.title}</h1>
              <p className="mt-6 text-gray-300 text-lg">{hero?.description}</p>
            </div>
          </section>
        )}
<div className="mb-12">
<div className="flex gap-3 overflow-x-auto md:overflow-visible md:flex-wrap md:justify-center pb-2 no-scrollbar">
    <Link
      href="/services"
      className={`flex-shrink-0 px-5 py-2 rounded-full text-sm font-medium transition-all duration-300 border ${
        !category
          ? "bg-[#00C2A8] text-white border-[#00C2A8] shadow-md"
          : "bg-white text-[#0B3C5D] border-gray-200 hover:border-[#00C2A8] hover:text-[#00C2A8]"
      }`}
    >
      ყველა
    </Link>

    {[
      { name: "Web Development", slug: "web-development" },
      { name: "Cyber Security", slug: "cyber-security" },
      { name: "Networking", slug: "networking" },
      { name: "IT Support", slug: "it-support" },
    ].map((cat) => (
      <Link
        key={cat.slug}
        href={`/services?category=${cat.slug}`}
        className={`flex-shrink-0 px-5 py-2 rounded-full text-sm font-medium transition-all duration-300 border ${
          category === cat.slug
            ? "bg-[#00C2A8] text-white border-[#00C2A8] shadow-md"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:border-[#00C2A8] hover:text-[#00C2A8]"
        }`}
      >
        {cat.name}
      </Link>
    ))}
  </div>
</div>
        {services.length > 0 && (
          <section className="py-24 bg-gradient-to-b from-white to-gray-50">
            <div className="max-w-7xl mx-auto px-4">
              <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-8">

                {services.map((service) => (
                  <Link key={service.slug} href={`/services/${service.slug}`}>
                    <div className="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 hover:-translate-y-2">

                      <div className="relative h-[220px] overflow-hidden">
                        <Image src={service.image || "/placeholder.jpg"} alt={service.title} fill className="object-cover transition duration-700 group-hover:scale-110" />
                        <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition" />
                      </div>

                      <div className="p-6">
                        <h3 className="text-lg font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                          {service.title}
                        </h3>

                        <p className="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-3">
                          {service.short_description}
                        </p>

                        <div className="mt-5 flex items-center justify-between">
                          <span className="text-sm text-[#00C2A8] font-medium">დეტალურად →</span>

                          <div className="w-8 h-8 rounded-full bg-[#00C2A8]/10 flex items-center justify-center group-hover:bg-[#00C2A8] transition">
                            <span className="text-[#00C2A8] group-hover:text-white text-sm">→</span>
                          </div>
                        </div>
                      </div>

                    </div>
                  </Link>
                ))}

              </div>
            </div>
          </section>
        )}
<div className="flex justify-center mt-16 gap-2 items-center">

  {/* PREV */}
  {page > 1 && (
    <Link
      href={`/services?page=${page - 1}${category ? `&category=${category}` : ""}`}
      className="w-10 h-10 flex items-center justify-center rounded-full border bg-white text-[#0B3C5D] hover:bg-[#00C2A8] hover:text-white transition"
    >
      ←
    </Link>
  )}

  {/* PAGES */}
  <div className="flex gap-2 overflow-x-auto no-scrollbar">
    {pages.map((p) => (
      <Link
        key={p}
        href={`/services?page=${p}${category ? `&category=${category}` : ""}`}
        className={`flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-full text-sm font-medium transition-all duration-300 border ${
          p === page
            ? "bg-[#0B3C5D] text-white border-[#0B3C5D] shadow-md"
            : "bg-white text-[#0B3C5D] border-gray-200 hover:bg-[#00C2A8] hover:text-white hover:border-[#00C2A8]"
        }`}
      >
        {p}
      </Link>
    ))}
  </div>

  {/* NEXT */}
  {page < totalPages && (
    <Link
      href={`/services?page=${page + 1}${category ? `&category=${category}` : ""}`}
      className="w-10 h-10 flex items-center justify-center rounded-full border bg-white text-[#0B3C5D] hover:bg-[#00C2A8] hover:text-white transition"
    >
      →
    </Link>
  )}

</div>
      </main>
    </>
  );
}