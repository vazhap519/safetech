

import Link from "next/link";
import Image from "next/image";
import { buildMetadata } from "@/lib/seo";
import { getServices, getSeoByKey } from "@/lib/datafetch";
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
// export default async function ServicesPage({ searchParams }) {

//   const params = await searchParams; // 🔥 აუცილებელია Next 16-ში
//   const page = Number(params?.page || 1);

//   const data = await getServices({ page });

//   if (!data || data.error) {
//     return (
//       <div className="text-center py-20 text-red-500">
//         სერვისები ვერ ჩაიტვირთა 😔
//       </div>
//     );
//   }

// const services = data?.data?.services ?? [];
// const meta = data?.data?.meta ?? {};
// const hero = data?.data?.serviceHero;
//   const totalPages = meta?.last_page || 1;

//   return (


//  <main>

//       {/* 🔥 HERO */}
//       <section className="relative py-28 text-white overflow-hidden 
//       bg-gradient-to-br from-[#071A2B] via-[#0B3C5D] to-[#0E4F73]">

//         {/* glow */}
//         <div className="absolute inset-0">
//           <div className="absolute top-[-100px] left-[-100px] w-[400px] h-[400px] bg-[#00C2A8]/20 blur-[120px]" />
//           <div className="absolute bottom-[-100px] right-[-100px] w-[400px] h-[400px] bg-[#00C2FF]/20 blur-[120px]" />
//         </div>

//         <div className="relative text-center max-w-3xl mx-auto px-4">

//           <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-3">
//             Safetech
//           </p>

//           <h1 className="text-4xl md:text-6xl font-bold leading-tight tracking-tight">
//             {hero?.title || "ჩვენი სერვისები"}
//           </h1>

//           <p className="mt-6 text-gray-300 text-lg leading-relaxed">
//             {hero?.description || "აირჩიე შენთვის საჭირო IT სერვისი"}
//           </p>

//         </div>
//       </section>

//       {/* 🔥 SERVICES GRID */}
//       <section className="py-24 bg-gradient-to-b from-white to-gray-50">

//         <div className="max-w-7xl mx-auto px-4">

//           {/* GRID */}
//           <div className="grid md:grid-cols-3 gap-10">

//             {services.map((service, i) => (
//               <Link key={service.slug} href={`/services/${service.slug}`}>

//                 <div
//                   className="group relative bg-white rounded-3xl overflow-hidden border border-gray-100 
//                   shadow-sm hover:shadow-2xl transition-all duration-700 hover:-translate-y-3"
//                   style={{
//                     animation: `fadeUp 0.6s ease forwards`,
//                     animationDelay: `${i * 100}ms`,
//                     opacity: 0,
//                   }}
//                 >

//                   {/* IMAGE */}
//                   <div className="relative h-[220px] overflow-hidden">

//                     <Image
//                       src={service.image || "/placeholder.jpg"}
//                       alt={service.title}
//                       fill
//                       sizes="(max-width:768px) 100vw, 33vw"
//                       className="object-cover transition duration-700 group-hover:scale-110"
//                     />

//                     {/* overlay */}
//                     <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent opacity-0 group-hover:opacity-100 transition"></div>

//                   </div>

//                   {/* CONTENT */}
//                   <div className="p-6">

//                     <h3 className="text-lg font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
//                       {service.title}
//                     </h3>

//                     <p className="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-3">
//                       {service.short_description}
//                     </p>

//                     {/* CTA */}
//                     <div className="mt-5 flex items-center justify-between">

//                       <span className="text-sm text-[#00C2A8] font-medium group-hover:translate-x-1 transition">
//                         დეტალურად →
//                       </span>

//                       <div className="w-8 h-8 rounded-full bg-[#00C2A8]/10 flex items-center justify-center group-hover:bg-[#00C2A8] transition">
//                         <span className="text-[#00C2A8] group-hover:text-white text-sm">
//                           →
//                         </span>
//                       </div>

//                     </div>

//                   </div>

//                   {/* glow hover */}
//                   <div className="absolute inset-0 rounded-3xl opacity-0 group-hover:opacity-100 
//                   bg-[#00C2A8]/5 blur-xl transition duration-500"></div>

//                 </div>

//               </Link>
//             ))}

//           </div>

//           {/* 🔥 PAGINATION */}
//      <div className="flex justify-center mt-20 gap-3 flex-wrap items-center">

//   {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => {
//     const isActive = p == page;

//     return (
//       <Link
//         key={p}
//         href={`/services?page=${p}`}
//         className={`relative w-11 h-11 flex items-center justify-center rounded-full text-sm font-medium transition-all duration-300
//         ${
//           isActive
//             ? "bg-[#00C2A8] text-white shadow-lg scale-110"
//             : "bg-white text-gray-600 border border-gray-200 hover:border-[#00C2A8] hover:text-[#00C2A8] hover:shadow-md"
//         }`}
//       >

//         {p}

//         {/* 🔥 active glow */}
//         {isActive && (
//           <span className="absolute inset-0 rounded-full bg-[#00C2A8]/30 blur-md"></span>
//         )}

//       </Link>
//     );
//   })}

// </div>
//         </div>

//       </section>

//     </main>
//   );
// }

export default async function ServicesPage({ searchParams }) {

  const params = await searchParams;
  const page = Number(params?.page || 1);

  const res = await getServices({ page });

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

  return (
    <>
      {/* ✅ DYNAMIC SCHEMA */}
      {seo?.schema && (
        Array.isArray(seo.schema) ? (
          seo.schema.map((schema, i) => (
            <script
              key={i}
              type="application/ld+json"
              dangerouslySetInnerHTML={{
                __html: JSON.stringify(schema),
              }}
            />
          ))
        ) : (
          <script
            type="application/ld+json"
            dangerouslySetInnerHTML={{
              __html: JSON.stringify(seo.schema),
            }}
          />
        )
      )}

      <main>

        {/* HERO */}
        <section className="relative py-28 text-white overflow-hidden 
        bg-gradient-to-br from-[#071A2B] via-[#0B3C5D] to-[#0E4F73]">

          <div className="relative text-center max-w-3xl mx-auto px-4">

            <p className="text-[#00C2A8] uppercase text-sm tracking-widest mb-3">
              Safetech
            </p>

            <h1 className="text-4xl md:text-6xl font-bold">
              {hero?.title || "ჩვენი სერვისები"}
            </h1>

            <p className="mt-6 text-gray-300 text-lg">
              {hero?.description}
            </p>

          </div>
        </section>

        {/* SERVICES */}
        {/* <section className="py-24">

          <div className="grid md:grid-cols-3 gap-10">

            {services.map((service) => (
              <Link key={service.slug} href={`/services/${service.slug}`}>
                <div>
                  <Image
                    src={service.image || "/placeholder.jpg"}
                    alt={service.title}
                    width={400}
                    height={250}
                  />
                  <h3>{service.title}</h3>
                  <p>{service.short_description}</p>
                </div>
              </Link>
            ))}

          </div>

        </section> */}
<section className="py-24 bg-gradient-to-b from-white to-gray-50">
  <div className="max-w-7xl mx-auto px-4">

    <div className="grid sm:grid-cols-2 md:grid-cols-3 gap-8">

      {services.map((service, i) => (
        <Link key={service.slug} href={`/services/${service.slug}`}>

          <div className="
            group
            bg-white
            rounded-2xl
            overflow-hidden
            border border-gray-100
            shadow-sm
            hover:shadow-xl
            transition-all duration-500
            hover:-translate-y-2
          ">

            {/* IMAGE */}
            <div className="relative h-[220px] overflow-hidden">
              <Image
                src={service.image || "/placeholder.jpg"}
                alt={service.title}
                fill
                className="object-cover transition duration-700 group-hover:scale-110"
              />

              {/* overlay */}
              <div className="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition" />
            </div>

            {/* CONTENT */}
            <div className="p-6">

              <h3 className="
                text-lg font-semibold text-[#0B3C5D]
                group-hover:text-[#00C2A8]
                transition
              ">
                {service.title}
              </h3>

              <p className="text-sm text-gray-500 mt-2 leading-relaxed line-clamp-3">
                {service.short_description}
              </p>

              {/* CTA */}
              <div className="mt-5 flex items-center justify-between">

                <span className="text-sm text-[#00C2A8] font-medium">
                  დეტალურად →
                </span>

                <div className="
                  w-8 h-8 rounded-full
                  bg-[#00C2A8]/10
                  flex items-center justify-center
                  group-hover:bg-[#00C2A8]
                  transition
                ">
                  <span className="text-[#00C2A8] group-hover:text-white text-sm">
                    →
                  </span>
                </div>

              </div>

            </div>

          </div>

        </Link>
      ))}

    </div>

  </div>
</section>
      </main>
    </>
  );
}