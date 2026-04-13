import { notFound } from "next/navigation";
import Image from "next/image";
import { getBlogPost, getSettings } from "@/lib/datafetch";
import SEOSection from "@/app/components/SEOSection";
import { injectInternalLinks } from "@/lib/internalLinks";
import Share from "../../components/Share";
import { getCurrentUrl } from "../../../lib/getUrl";

export const revalidate = 300;

const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

/* =========================
   ?? METADATA
========================= */
export async function generateMetadata({ params }) {
  const { slug } = params;

  if (!slug) {
    return {
      title: "?????",
      description: "Safetech ?????",
    };
  }

  try {
    const res = await getBlogPost(slug);

    if (!res || res.error || !res.data) {
      return {
        title: "?????",
        description: "?????? ??? ????????",
      };
    }

    const post = res.data;
    const seo = post?.seo?.meta || {};

    const title = seo.title || post.title;
    const description = seo.description || post.excerpt;

    const image =
      seo.image ||
      post.image ||
      DEFAULT_IMAGE;

    const url = await getCurrentUrl(`/blog/${slug}`);

    return {
      title,
      description,

      alternates: {
        canonical: url,
      },

      openGraph: {
        title,
        description,
        url,
        type: "article",
        images: [
          {
            url: image,
            width: 1200,
            height: 630,
          },
        ],
      },

      twitter: {
        card: "summary_large_image",
        title,
        description,
        images: [image],
      },
    };

  } catch {
    return {
      title: "?????",
      description: "Safetech ?????",
    };
  }
}

/* =========================
   ?? SCHEMA
========================= */
function buildSchema(post, url) {
  const seo = post?.seo?.meta || {};

  return {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    headline: seo.title || post.title,
    description: seo.description || post.excerpt,
    image: post.image || DEFAULT_IMAGE,
    author: {
      "@type": "Organization",
      name: post.author?.name || "Safetech",
    },
    publisher: {
      "@type": "Organization",
      name: "Safetech",
    },
    datePublished:
      post.created_at || new Date().toISOString(),
    mainEntityOfPage: url,
  };
}

/* =========================
   PAGE
========================= */
export default async function BlogDetailPage({ params }) {
  const { slug } = params;

  if (!slug) return notFound();

  const res = await getBlogPost(slug);

  if (!res || res.error || !res.data) {
    return notFound();
  }

  const post = res.data;
  const settings = await getSettings();

  const url = await getCurrentUrl(`/blog/${post.slug}`);
  const schema = buildSchema(post, url);

  return (
    <main className="py-20 bg-[#F8FAFC]">

      {/* ?? SCHEMA */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify(schema),
        }}
      />

      <article className="max-w-4xl mx-auto px-4">

        <h1 className="text-4xl font-bold text-[#0B3C5D]">
          {post.title}
        </h1>

        <Image
          src={
            post.image?.startsWith("http")
              ? post.image
              : `${process.env.NEXT_PUBLIC_API_URL}${post.image || DEFAULT_IMAGE}`
          }
          alt={post.title}
          width={1200}
          height={600}
          className="mt-6 rounded-xl w-full"
        />

        <p className="mt-6 text-lg text-gray-600">
          {post.excerpt}
        </p>

        <Share data={settings?.share ?? {}} url={url} />

        <div className="mt-10 space-y-6 text-gray-700">
          {post.sections?.map((section, i) => (
            <div key={i}>
              {section.title && (
                <h2 className="text-2xl font-bold text-[#0B3C5D] mt-10">
                  {section.title}
                </h2>
              )}

              <div
                className="mt-4"
                dangerouslySetInnerHTML={{
                  __html: injectInternalLinks(section.content, []),
                }}
              />
            </div>
          ))}
        </div>

        <SEOSection
          title="?????????? ??????????"
          paragraphs={post.seo?.content || [post.excerpt]}
          links={[]}
        />

      </article>
    </main>
  );
}