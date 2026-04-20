// import Link from "next/link";
// import Image from "next/image";
// import { buildMetadata } from "@/lib/seo";

// import {
//   getServices,
//   getSeoByKey,
//   getCategories,
//   getEmpty,
// } from "@/lib/datafetch";

// import EmptyState from "../components/ui/EmptyState";

// export const revalidate = 60; // ? caching balance

// /* =========================
//    SEO
// ========================= */
// export async function generateMetadata() {
//   const seo = await getSeoByKey("services");
//   const data = seo?.data;

//   return buildMetadata({
//     title: data?.title || "Services",
//     description: data?.description,
//     image: data?.og?.image,
//     keywords: data?.keywords,
//     canonical: data?.canonical,
//     noindex: data?.noindex,
//     og: data?.og,
//     path: data?.slug || "/services",
//   });
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function ServicesPage({ searchParams }) {
//   const page = Number(searchParams?.page || 1);
//   const category = searchParams?.category || null;

//   // ?? PARALLEL FETCH (performance boost)
//   const [servicesRes, categoriesRes] = await Promise.all([
//     getServices({ page, category }),
//     getCategories(),
//   ]);

//   /* ? ERROR */
//   if (!servicesRes || servicesRes.error) {
//     const empty = await getEmpty();
//     return <EmptyState empty={empty} />;
//   }

//   const data = servicesRes.data;
//   const seo = servicesRes.seo;

//   const services = data?.services ?? [];
//   const meta = data?.meta ?? {};
//   const hero = data?.serviceHero;

//   const categories = categoriesRes?.data ?? [];

//   /* ?? EMPTY */
//   if (!services.length) {
//     const empty = await getEmpty();
//     return <EmptyState empty={empty} />;
//   }

//   /* ?? PAGINATION */
//   const totalPages = meta?.last_page || 1;
//   const start = Math.max(1, page - 2);
//   const end = Math.min(totalPages, page + 2);

//   const pages = [];
//   for (let i = start; i <= end; i++) pages.push(i);

//   return (
//     <>
//       {/* ?? SCHEMA */}
//       {seo?.schema &&
//         (Array.isArray(seo.schema)
//           ? seo.schema.map((schema, i) => (
//               <script
//                 key={i}
//                 type="application/ld+json"
//                 dangerouslySetInnerHTML={{
//                   __html: JSON.stringify(schema),
//                 }}
//               />
//             ))
//           : (
//               <script
//                 type="application/ld+json"
//                 dangerouslySetInnerHTML={{
//                   __html: JSON.stringify(seo.schema),
//                 }}
//               />
//             ))}

//       <main>

//         {/* HERO */}
//         {hero && (
//           <section className="py-28 text-white text-center bg-gradient-to-br from-[#071A2B] via-[#0B3C5D] to-[#0E4F73]">
//             <h1 className="text-4xl md:text-6xl font-bold">{hero?.title}</h1>
//             <p className="mt-6 text-gray-300">{hero?.description}</p>
//           </section>
//         )}

//         {/* FILTERS */}
//         <div className="my-10 flex flex-wrap justify-center gap-3 px-4">

//           {/* ALL */}
//           <Link
//             href="/services"
//             className={`px-5 py-2 rounded-full text-sm font-medium border ${
//               !category
//                 ? "bg-[#00C2A8] text-white"
//                 : "bg-white text-[#0B3C5D]"
//             }`}
//           >
//             ?????
//           </Link>

//           {categories.map((cat) => (
//             <Link
//               key={cat.slug}
//               href={`/services?category=${cat.slug}`}
//               className={`px-5 py-2 rounded-full text-sm font-medium border ${
//                 category === cat.slug
//                   ? "bg-[#00C2A8] text-white"
//                   : "bg-white text-[#0B3C5D]"
//               }`}
//             >
//               {cat.name}
//             </Link>
//           ))}
//         </div>

//         {/* SERVICES GRID */}
//         <section className="py-16 bg-gray-50">
//           <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

//             {services.map((service) => (
//               <Link key={service.slug} href={`/services/${service.slug}`}>
//                 <div className="bg-white rounded-2xl overflow-hidden shadow hover:shadow-xl transition">

//                   <div className="relative h-[220px]">
//                     <Image
//                       src={service.image || "/placeholder.jpg"}
//                       alt={service.title}
//                       fill
//                       className="object-cover"
//                     />
//                   </div>

//                   <div className="p-6">
//                     <h3 className="font-semibold text-[#0B3C5D]">
//                       {service.title}
//                     </h3>

//                     <p className="text-sm text-gray-500 mt-2 line-clamp-3">
//                       {service.short_description}
//                     </p>
//                   </div>

//                 </div>
//               </Link>
//             ))}

//           </div>
//         </section>

//         {/* PAGINATION */}
//         <div className="flex justify-center gap-2 mt-10">

//           {page > 1 && (
//             <Link href={`/services?page=${page - 1}${category ? `&category=${category}` : ""}`}>
//               ?
//             </Link>
//           )}

//           {pages.map((p) => (
//             <Link
//               key={p}
//               href={`/services?page=${p}${category ? `&category=${category}` : ""}`}
//               className={p === page ? "font-bold" : ""}
//             >
//               {p}
//             </Link>
//           ))}

//           {page < totalPages && (
//             <Link href={`/services?page=${page + 1}${category ? `&category=${category}` : ""}`}>
//               ?
//             </Link>
//           )}

//         </div>

//       </main>
//     </>
//   );
// }


import Link from "next/link";
import Image from "next/image";
import { buildMetadata } from "@/lib/seo";
import { getBaseUrl } from "@/lib/config";

import {
  getServices,
  getSeoByKey,
  getServiceCategories,
  getEmpty,
} from "@/lib/datafetch";

import EmptyState from "../components/ui/EmptyState";
import CategoryFilters from "../components/ui/CategoryFilters";
import Pagination from "../components/ui/Pagination";


export const revalidate = 60; // ? caching balance

/* =========================
   SEO
========================= */
// export async function generateMetadata() {
//   const seo = await getSeoByKey("services");
//   const data = seo?.data;

//   return buildMetadata({
//     title: data?.title || "Services",
//     description: data?.description,
//     image: data?.og?.image,
//     keywords: data?.keywords,
//     canonical: data?.canonical,
//     noindex: data?.noindex,
//     og: data?.og,
//     path: data?.slug || "/services",
//   });
// }

// export async function generateMetadata({ searchParams }) {
//   const seo = await getSeoByKey("services");
//   const data = seo?.data;

//   const base = "https://safetech.ge";

//   const page = Number(searchParams?.page || 1);
//   const category = searchParams?.category || null;

//   let url = `${base}/services`;

//   if (category) {
//     url = `${base}/services/category/${category}`;
//   }

//   if (page > 1) {
//     url += `/page/${page}`;
//   }

//   /* 🔥 NOINDEX RULE */
//   let noindex = false;

//   if (page > 5) {
//     noindex = true;
//   }

//   return {
//     ...buildMetadata({
//       title: data?.title || "Services",
//       description: data?.description,
//       image: data?.og?.image,
//       keywords: data?.keywords,
//       og: data?.og,
//       path: data?.slug || "/services",
//     }),

//     alternates: {
//       canonical: url,
//     },

//     robots: {
//       index: !noindex,
//       follow: true,
//     },
//   };
// }

export async function generateMetadata({ searchParams }) {
  const params = await searchParams;
  const seo = await getSeoByKey("services").catch(() => null);
  const data = seo?.data || {};

  const page = Number(params?.page || 1);
  const category = params?.category || null;

  const BASE_URL = getBaseUrl();
  let url = `${BASE_URL}/services`;

  if (category) {
    url += `?category=${category}`;
  }

  if (page > 1) {
    url += `${category ? "&" : "?"}page=${page}`;
  }

  return {
    ...buildMetadata({
      title: data?.title || "IT სერვისები",
      description:
        data?.description ||
        "Safetech გთავაზობთ IT მხარდაჭერას, კომპიუტერულ სერვისებს, ქსელების გამართვას და უსაფრთხოების სისტემების მონტაჟს.",
      image: data?.og?.image,
      keywords: data?.keywords,
      noindex: page > 5 || data?.noindex,
      og: data?.og,
      path: "/services",
    }),
    alternates: {
      canonical: url,
    },
  };
}
export default async function ServicesPage({ searchParams }) {
  // ✅ FIX 1: await searchParams
  const params = await searchParams;

  const page = Number(params?.page || 1);
  const category = params?.category || null;

  // ?? PARALLEL FETCH (performance boost)
  const [servicesRes, categoriesRes] = await Promise.all([
    getServices({ page, category }),
    getServiceCategories(),
  ]);

  /* ? ERROR */
  if (!servicesRes || servicesRes.error) {
    const empty = await getEmpty();
    return <EmptyState empty={empty} />;
  }

  const data = servicesRes.data;
  const seo = servicesRes.seo;

  const services = data?.services ?? [];
  const meta = data?.meta ?? {};
  const hero = data?.serviceHero;

  const categories = Array.isArray(categoriesRes)
    ? categoriesRes
    : categoriesRes?.data ?? [];

  // ✅ FIX 2: აღარ ვაბრუნებთ მთელ გვერდს
  const isEmpty = !services.length;

  /* ?? PAGINATION */
  const totalPages = meta?.last_page || 1;

  return (
    <>
      {/* ?? SCHEMA */}
      {seo?.schema &&
        (Array.isArray(seo.schema)
          ? seo.schema.map((schema, i) => (
              <script
                key={i}
                type="application/ld+json"
                dangerouslySetInnerHTML={{
                  __html: JSON.stringify(schema),
                }}
              />
            ))
          : (
              <script
                type="application/ld+json"
                dangerouslySetInnerHTML={{
                  __html: JSON.stringify(seo.schema),
                }}
              />
            ))}

      <main>

        {/* HERO */}
        {hero && (
          <section className="py-28 text-white text-center bg-gradient-to-br from-[#071A2B] via-[#0B3C5D] to-[#0E4F73]">
            <h1 className="text-4xl md:text-6xl font-bold">{hero?.title}</h1>
            <p className="mt-6 text-gray-300">{hero?.description}</p>
          </section>
        )}

        {/* FILTERS */}
        <div className="my-10 px-4">
          <CategoryFilters
            basePath="/services"
            categories={categories}
            currentCategory={category || "all"}
          />
        </div>

        {/* SERVICES GRID */}
        <section className="py-16 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

            {/* ✅ FIX 3: empty UI */}
            {isEmpty ? (
              <p className="col-span-3 text-center text-gray-500">
                სერვისები ვერ მოიძებნა
              </p>
            ) : (
              services.map((service) => (
                <Link key={service.slug} href={`/services/${service.slug}`}>
                  <div className="bg-white rounded-2xl overflow-hidden shadow hover:shadow-xl transition">

                    <div className="relative h-[220px]">
                      <Image
                        src={service.image || "/placeholder.jpg"}
                        alt={service.title}
                        fill
                        sizes="(max-width: 768px) 100vw, 33vw"
                        className="object-cover"
                      />
                    </div>

                    <div className="p-6">
                      <h3 className="font-semibold text-[#0B3C5D]">
                        {service.title}
                      </h3>

                      <p className="text-sm text-gray-500 mt-2 line-clamp-3">
                        {service.short_description}
                      </p>
                    </div>

                  </div>
                </Link>
              ))
            )}

          </div>
        </section>

        <Pagination
          basePath="/services"
          category={category || "all"}
          currentPage={page}
          lastPage={totalPages}
        />
      </main>
    </>
  );
}
