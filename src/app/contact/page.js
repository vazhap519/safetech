// import { buildMetadata } from "@/lib/seo";

// import ContactHero from "@/app/components/contact/ContactHero";
// import ContactInfo from "@/app/components/contact/ContactInfo";
// import ContactForm from "@/app/components/contact/ContactForm";
// import ContactWhyUs from "@/app/components/contact/ContactWhyUs";
// // import ContactCTA from "@/app/components/contact/ContactCTA";
// import { getContact,getSeoByKey } from "@/lib/datafetch";
// /* =========================
//    PAGE
// ========================= */
// /* =========================
//    SEO (Contact 🔥)
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




// export default async function ContactPage() {
//   const contactpage = await getContact();
// const contact = contactpage;

//   return (
//     <main>

//       {/* 🔥 FIX აქ */}
//       <ContactHero data={contact} />

//       <section className="py-20 bg-[#F8FAFC]">
//         <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10">

//           <div>
//             <ContactInfo data={contact} />
//             <ContactWhyUs data={contact} />
//           </div>

//           <ContactForm />

//         </div>
//       </section>

//       {/* <ContactCTA data={contact} /> */}

//     </main>
//   );
// }

import { buildMetadata } from "@/lib/seo";

import ContactHero from "@/app/components/contact/ContactHero";
import ContactInfo from "@/app/components/contact/ContactInfo";
import ContactForm from "@/app/components/contact/ContactForm";
import ContactWhyUs from "@/app/components/contact/ContactWhyUs";

import { getContact, getSeoByKey } from "@/lib/datafetch";

/* =========================
   SEO (CONTACT 🔥)
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("contact");

  const data = seo?.data;

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

  // ✅ unified response (როგორც სხვა გვერდებზე)
  const res = await getContact();

  if (!res || res.error) {
    return (
      <div className="text-center py-20 text-red-500">
        გვერდი ვერ ჩაიტვირთა 😔
      </div>
    );
  }

  const data = res.data;
  const seo = res.seo;

  return (
    <>
      {/* 🔥 DYNAMIC SCHEMA */}
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