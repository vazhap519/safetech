import Link from "next/link";
import { getPrivacy,getSeoByKey  } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";
/* =========================
   SEO (privacy 🔥)
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
export default async function PrivacyPage() {
    const privacy=await getPrivacy()

  return (
    <main className="bg-white text-gray-800">

      {/* 🔥 HERO */}
      <section className="bg-[#0B3C5D] text-white py-20">
        <div className="max-w-5xl mx-auto px-4 text-center">
          <h1 className="text-3xl md:text-4xl font-bold mb-4">
            {privacy?.title || "Privacy Policy"}
          </h1>
          <p className="text-gray-300 max-w-2xl mx-auto">
             {privacy?.highlight || "Privacy Policy"}
          </p>
        </div>
      </section>

      {/* 🔥 CONTENT */}
<section className="max-w-5xl mx-auto px-4 py-20">

  <div className="bg-white rounded-2xl shadow-lg p-6 md:p-10">

    <div
      className="prose max-w-none prose-lg
                 prose-h2:text-[#0B3C5D]
                 prose-h2:font-bold
                 prose-h2:mt-10
                 prose-p:text-gray-600
                 prose-li:text-gray-600
                 prose-li:marker:text-[#00C2A8]
                 prose-strong:text-[#0B3C5D]
                 prose-a:text-[#00C2A8]
                 prose-a:no-underline hover:prose-a:underline"
      dangerouslySetInnerHTML={{
        __html: privacy?.content || "<p>No content</p>",
      }}
    />

  </div>

  {/* 🔥 BACK */}
  <div className="pt-12 text-center">
    <Link
      href="/"
      className="inline-flex items-center gap-2 bg-[#00C2A8] text-white px-6 py-3 rounded-xl shadow hover:scale-105 transition"
    >
      ← მთავარ გვერდზე დაბრუნება
    </Link>
  </div>

</section>
    </main>
  );
}