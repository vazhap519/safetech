
// import Link from "next/link";
// import Image from "next/image";
// import { buildMetadata } from "@/lib/seo";
// import { getServices } from "@/lib/datafetch";

// /* =========================
//    META SEO (dynamic)
// ========================= */
// export async function generateMetadata() {
//   return buildMetadata({
//     title: "IT სერვისები საქართველოში",
//     description:
//       "Safetech გთავაზობთ კამერების მონტაჟს, POS სისტემებს და IT სერვისებს.",
//     path: "/services",
//   });
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function ServicesPage() {
// const data = await getServices({
//   next: { revalidate: 60 }, // 🔥 აქ უნდა იყოს
// });
//   const services = data?.services || [];

//   return (
//     <main>

//       {/* JSON-LD */}
//       <script
//         type="application/ld+json"
//         dangerouslySetInnerHTML={{
//           __html: JSON.stringify({
//             "@context": "https://schema.org",
//             "@type": "ItemList",
//             itemListElement: services.map((service, index) => ({
//               "@type": "ListItem",
//               position: index + 1,
//               name: service.title,
//               url: `https://safetech.ge/services/${service.slug}`,
//             })),
//           }),
//         }}
//       />

//       {/* HERO */}
//       <section className="py-20 bg-[#0B3C5D] text-white text-center">
//         <h1 className="text-4xl md:text-5xl font-bold">
//           ჩვენი სერვისები
//         </h1>

//         <p className="mt-4 text-gray-300 max-w-xl mx-auto">
//           აირჩიე შენთვის საჭირო IT და უსაფრთხოების სერვისი და მიიღე პროფესიონალური დახმარება
//         </p>
//       </section>

//       {/* SERVICES GRID */}
//       <section className="py-20 bg-[#F8FAFC]">
//         <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

//           {services.map((service) => (
//             <Link key={service.slug} href={`/services/${service.slug}`}>
//               <div className="group bg-white rounded-2xl overflow-hidden shadow-sm 
//                 hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] 
//                 transition-all duration-300 cursor-pointer">

//                 {/* Image */}
//                 <div className="relative overflow-hidden">
//                   <Image
//                     src={service.image || "/placeholder.jpg"}
//                     alt={service.title}
//                     width={500}
//                     height={300}
//                     className="w-full h-44 object-cover group-hover:scale-110 transition duration-300"
//                   />
//                 </div>

//                 {/* Content */}
//                 <div className="p-6">
//                   <h3 className="font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
//                     {service.title}
//                   </h3>

//                   <p className="text-sm text-gray-600 mt-2 leading-relaxed">
//                     {service.description}
//                   </p>

//                   <div className="mt-4 text-sm text-[#00C2A8] font-medium">
//                     დეტალურად →
//                   </div>
//                 </div>

//               </div>
//             </Link>
//           ))}

//         </div>
//       </section>

//     </main>
//   );
// }


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

 const params = await searchParams; // 🔥 ეს დაამატე
  const page = Number(params?.page || 1);
  const data = await getServices(page, {
    next: { revalidate: 60 },
  });

  const services = data?.services || [];
  const meta = data?.meta;
const hero = data?.ServiceHero;
console.log(hero)
  const totalPages = meta?.last_page || 1;

  return (
    <main>

      {/* JSON-LD */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify({
            "@context": "https://schema.org",
            "@type": "ItemList",
            itemListElement: services.map((service, index) => ({
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
         {hero?.title || "ჩვენი სერვისები"}
        </h1>

        <p className="mt-4 text-gray-300 max-w-xl mx-auto">
          {hero?.description || "აირჩიე შენთვის საჭირო IT და უსაფრთხოების სერვისი"}
        </p>
      </section>

      {/* SERVICES GRID */}
      <section className="py-20 bg-[#F8FAFC]">
        <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-3 gap-8">

          {services.map((service) => (
            <Link key={service.slug} href={`/services/${service.slug}`}>
              <div className="group bg-white rounded-2xl overflow-hidden shadow-sm 
                hover:shadow-xl hover:-translate-y-1 hover:scale-[1.02] 
                transition-all duration-300 cursor-pointer">

                <div className="relative overflow-hidden">
                  <Image
                    src={service.image || "/placeholder.jpg"}
                    alt={service.title}
                    width={500}
                    height={300}
                    className="w-full h-44 object-cover group-hover:scale-110 transition duration-300"
                  />
                </div>

                <div className="p-6">
                  <h3 className="font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8] transition">
                    {service.title}
                  </h3>

                  <p className="text-sm text-gray-600 mt-2 leading-relaxed">
                    {service.description}
                  </p>

                  <div className="mt-4 text-sm text-[#00C2A8] font-medium">
                    დეტალურად →
                  </div>
                </div>

              </div>
            </Link>
          ))}

        </div>

        {/* 🔥 PAGINATION UI */}
<div className="flex justify-center mt-16">

  <div className="flex items-center gap-2 bg-white shadow-md rounded-2xl px-4 py-2">

    {/* PREV */}
    <Link
      href={`/services?page=${page - 1}`}
      className={`px-4 py-2 rounded-xl text-sm font-semibold transition ${
        page === 1
          ? "opacity-40 pointer-events-none text-gray-400"
          : "text-[#0B3C5D] hover:bg-gray-200"
      }`}
    >
      ←
    </Link>

    {/* PAGE NUMBERS */}
    {Array.from({ length: totalPages }, (_, i) => i + 1).map((p) => (
      <Link
        key={p}
        href={`/services?page=${p}`}
        className={`w-10 h-10 flex items-center justify-center rounded-xl text-sm font-bold transition ${
          p === page
            ? "bg-[#00C2A8] text-white shadow-md"
            : "text-[#0B3C5D] bg-gray-100 hover:bg-gray-200"
        }`}
      >
        {p}
      </Link>
    ))}

    {/* NEXT */}
    <Link
      href={`/services?page=${page + 1}`}
      className={`px-4 py-2 rounded-xl text-sm font-semibold transition ${
        page === totalPages
          ? "opacity-40 pointer-events-none text-gray-400"
          : "text-[#0B3C5D] hover:bg-gray-200"
      }`}
    >
      →
    </Link>

  </div>

</div>

      </section>

    </main>
  );
}