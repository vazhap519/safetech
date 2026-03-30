
import Link from "next/link";
import { getPrivacy, getSeoByKey } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";
export const revalidate = 300;
/* =========================
   SEO
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("privacy");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/privacy",
  });
}

/* =========================
   PAGE
========================= */
export default async function PrivacyPage() {

  // ✅ unified response
  const res = await getPrivacy();

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

      <main className="bg-white text-gray-900">

        {/* HERO */}
        <section className="
          py-24
          bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B]
          text-white
        ">
          <div className="max-w-4xl mx-auto px-6 text-center">

            <h1 className="text-4xl md:text-5xl font-bold tracking-tight">
              {data?.title || "Privacy Policy"}
            </h1>

            <p className="mt-6 text-white/80 max-w-2xl mx-auto text-lg leading-relaxed">
              {data?.highlight || "ჩვენ ვიცავთ თქვენს მონაცემებს"}
            </p>

          </div>
        </section>

        {/* CONTENT */}
        <section className="py-24">
          <div className="max-w-4xl mx-auto px-6">

            <div className="
              bg-white
              border border-gray-200
              rounded-3xl
              p-8 md:p-12
            ">

              <div
                className="
                  prose prose-lg max-w-none
                  prose-h2:mt-12
                "
                dangerouslySetInnerHTML={{
                  __html: data?.content || "<p>No content</p>",
                }}
              />

            </div>

            {/* BACK BUTTON */}
            <div className="pt-12 text-center">
              <Link
                href="/"
                className="
                  inline-flex items-center gap-2
                  bg-[#00E0B8]
                  text-black
                  px-6 py-3
                  rounded-2xl
                  font-semibold
                  hover:scale-105
                  transition-all duration-300
                "
              >
                ← მთავარ გვერდზე დაბრუნება
              </Link>
            </div>

          </div>
        </section>

      </main>
    </>
  );
}