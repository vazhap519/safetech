
import AboutCTA from "../components/about/AboutCTA";
import AboutHero from "../components/about/AboutHero";
import AboutStory from "../components/about/AboutStory";
import AboutWhyUs from "../components/about/AboutWhyUs";

import { getAbout, getSeoByKey } from "@/lib/datafetch";
import { buildMetadata } from "@/lib/seo";
import EmptyState from "../components/ui/EmptyState";
import { getEmpty } from "@/lib/datafetch";
export const revalidate = 300;
/* =========================
   SEO
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("about");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/about",
  });
}

/* =========================
   PAGE
========================= */
export default async function AboutPage() {

  // ✅ backend unified response (როგორც services/blog)
  const res = await getAbout();
const empty=await getEmpty();


  const data = res.data;
  const seo = res.seo;

  const Hero = data?.Hero;
  const Story = data?.Story;
  const Why = data?.Why;
  const Cta = data?.Cta;
const isEmpty =
  !Hero ||
  !Story ||
  !Why ||
  !Cta ||
  Cta.length === 0;

if (!res || res.error || isEmpty) {
  return <EmptyState empty={empty} />;
}
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
        <AboutHero Hero={Hero} />
        <AboutStory Story={Story} />
        <AboutWhyUs Why={Why} />
        <AboutCTA Cta={Cta} />
      </main>
    </>
  );
}