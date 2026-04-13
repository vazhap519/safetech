import { buildMetadata } from "@/lib/seo";

import ContactHero from "@/app/components/contact/ContactHero";
import ContactInfo from "@/app/components/contact/ContactInfo";
import ContactForm from "@/app/components/contact/ContactForm";
import ContactWhyUs from "@/app/components/contact/ContactWhyUs";

import { getContact, getSeoByKey, getEmpty } from "@/lib/datafetch";
import EmptyState from "../components/ui/EmptyState";

export const revalidate = 300;

/* =========================
   SEO
========================= */
export async function generateMetadata() {
  try {
    const seo = await getSeoByKey("contact");

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
  } catch {
    return buildMetadata({
      title: "Contact",
      description: "Contact page",
      path: "/contact",
    });
  }
}

/* =========================
   PAGE
========================= */
export default async function ContactPage() {
  let res = null;

  try {
    res = await getContact();
  } catch {
    res = null;
  }

  /* ? API ERROR */
  if (!res || res.error) {
    const empty = await getEmpty().catch(() => null);
    return <EmptyState empty={empty} />;
  }

  const data = res.data;
  const seo = res.seo;

  /* ?? EMPTY CHECK (???????????? ?????) */
  const isEmpty =
    !data ||
    (!data.hero &&
      !data.phone &&
      !data.info &&
      !data.why);

  if (isEmpty) {
    const empty = await getEmpty().catch(() => null);
    return <EmptyState empty={empty} />;
  }

  return (
    <>
      {/* ?? SCHEMA SAFE */}
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
        {data?.hero && <ContactHero data={data} />}

        <section className="py-20 bg-[#F8FAFC]">
          <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10">

            <div>
              {data?.info && <ContactInfo data={data} />}
              {data?.why && <ContactWhyUs data={data} />}
            </div>

            <ContactForm />
          </div>
        </section>
      </main>
    </>
  );
}