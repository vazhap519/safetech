import { buildMetadata } from "@/lib/seo";

import ContactHero from "@/app/components/contact/ContactHero";
import ContactInfo from "@/app/components/contact/ContactInfo";
import ContactForm from "@/app/components/contact/ContactForm";
import ContactWhyUs from "@/app/components/contact/ContactWhyUs";
// import ContactCTA from "@/app/components/contact/ContactCTA";
import { getSettings,getSeoByKey } from "@/lib/datafetch";

/* =========================
   PAGE
========================= */
/* =========================
   SEO (Contact 🔥)
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




export default async function ContactPage() {
  const settings = await getSettings();
  const contact = settings?.contact_page;

  return (
    <main>

      {/* 🔥 FIX აქ */}
      <ContactHero data={contact} />

      <section className="py-20 bg-[#F8FAFC]">
        <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10">

          <div>
            <ContactInfo data={contact} />
            <ContactWhyUs data={contact} />
          </div>

          <ContactForm />

        </div>
      </section>

      {/* <ContactCTA data={contact} /> */}

    </main>
  );
}