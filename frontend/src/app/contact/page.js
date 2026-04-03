

// import { buildMetadata } from "@/lib/seo";

// import ContactHero from "@/app/components/contact/ContactHero";
// import ContactInfo from "@/app/components/contact/ContactInfo";
// import ContactForm from "@/app/components/contact/ContactForm";
// import ContactWhyUs from "@/app/components/contact/ContactWhyUs";

// import { getContact, getSeoByKey } from "@/lib/datafetch";
// export const revalidate = 300;
// /* =========================
//    SEO (CONTACT 🔥)
// ========================= */
// export async function generateMetadata() {
//   const seo = await getSeoByKey("contact");

//   const data = seo?.data;

//   return buildMetadata({
//     title: data?.title,
//     description: data?.description,
//     image: data?.og?.image,
//     keywords: data?.keywords,
//     canonical: data?.canonical,
//     noindex: data?.noindex,
//     og: data?.og,
//     path: data?.slug || "/contact",
//   });
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function ContactPage() {

//   // ✅ unified response (როგორც სხვა გვერდებზე)
//   const res = await getContact();

//   if (!res || res.error) {
//     return (
//       <div className="text-center py-20 text-red-500">
//         გვერდი ვერ ჩაიტვირთა 😔
//       </div>
//     );
//   }

//   const data = res.data;
//   const seo = res.seo;

//   return (
//     <>
//       {/* 🔥 DYNAMIC SCHEMA */}
//       {seo?.schema && (
//         Array.isArray(seo.schema) ? (
//           seo.schema.map((schema, i) => (
//             <script
//               key={i}
//               type="application/ld+json"
//               dangerouslySetInnerHTML={{
//                 __html: JSON.stringify(schema),
//               }}
//             />
//           ))
//         ) : (
//           <script
//             type="application/ld+json"
//             dangerouslySetInnerHTML={{
//               __html: JSON.stringify(seo.schema),
//             }}
//           />
//         )
//       )}

//       <main>

//         {/* HERO */}
//         <ContactHero data={data} />

//         <section className="py-20 bg-[#F8FAFC]">
//           <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10">

//             <div>
//               <ContactInfo data={data} />
//               <ContactWhyUs data={data} />
//             </div>

//             <ContactForm />

//           </div>
//         </section>

//       </main>
//     </>
//   );
// }


import { buildMetadata } from "@/lib/seo";

import ContactHero from "@/app/components/contact/ContactHero";
import ContactInfo from "@/app/components/contact/ContactInfo";
import ContactForm from "@/app/components/contact/ContactForm";
import ContactWhyUs from "@/app/components/contact/ContactWhyUs";

import { getContact, getSeoByKey } from "@/lib/datafetch";
import EmptyState from "../components/ui/EmptyState";
import { getEmpty } from "@/lib/datafetch";
export const revalidate = 300;

/* =========================
   SEO (SAFE 🔥)
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("contact");

  // 🔥 CRITICAL FIX
  if (!seo || !seo.data) {
    return buildMetadata({
      title: "Contact",
      description: "Contact page",
      path: "/contact",
    });
  }

  const data = seo.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/contact",
  });
}

/* =========================
   PAGE
========================= */
export default async function ContactPage() {
  const res = await getContact();


  const data = res?.data || {};
  const seo = res?.seo || {};


  let empty = null;
  try {
    empty = await getEmpty();
  } catch {
    empty = null;
  }

  /* ❌ ERROR */
  if (!res || res.error) {
    return <EmptyState empty={empty} />;
  }

 

  /* 📭 EMPTY DATA (შენი სტრუქტურის მიხედვით) */
  const isEmpty =
    !data ||
    Object.keys(data).length === 0;

  if (isEmpty) {
    return <EmptyState empty={empty} />;
  }






  return (
    <>
      {/* 🔥 SCHEMA SAFE */}
      {seo?.schema &&
        (Array.isArray(seo.schema) ? (
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
        ))}

      <main>
        {/* HERO */}
        <ContactHero data={data} />

        <section className="py-20 bg-[#F8FAFC]">
          <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10">
            <div>
              <ContactInfo data={data} />
              <ContactWhyUs data={data} />
            </div>

            <ContactForm />
          </div>
        </section>
      </main>
    </>
  );
}