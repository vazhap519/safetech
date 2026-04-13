

import { notFound } from "next/navigation";
import Image from "next/image";
import { getBlogPost, getSettings } from "@/lib/datafetch";
import SEOSection from "@/app/components/SEOSection";
import { injectInternalLinks } from "@/lib/internalLinks";
import Share from "../../components/Share";
import { getCurrentUrl } from "../../../lib/getUrl";
import { getSeoLinks } from "@/lib/getSeoLinks";
export const revalidate = 300;

const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

/* =========================
   🔥 METADATA
========================= */
export async function generateMetadata({ params }) {
  const { slug } = params;

  if (!slug) {
    return {
      title: "ბლოგი",
      description: "Safetech ბლოგი",
    };
  }

  try {
    const res = await getBlogPost(slug);

    if (!res || res.error || !res.data) {
      return {
        title: "ბლოგი",
        description: "სტატია ვერ მოიძებნა",
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
      title: "ბლოგი",
      description: "Safetech ბლოგი",
    };
  }
}

/* =========================
   🔥 BLOG SCHEMA (PRO)
========================= */
function buildSchema(post, url) {
  const seo = post?.seo?.meta || {};

  const image =
    post.image?.startsWith("http")
      ? post.image
      : `${process.env.NEXT_PUBLIC_API_URL}${post.image || DEFAULT_IMAGE}`;

  return {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "@id": url,

    headline: seo.title || post.title,
    description: seo.description || post.excerpt,

    image: {
      "@type": "ImageObject",
      url: image,
      width: 1200,
      height: 630,
    },

    author: {
      "@type": "Organization",
      name: post.author?.name || "SafeTech",
    },

    publisher: {
      "@type": "Organization",
      name: "SafeTech",
      logo: {
        "@type": "ImageObject",
        url: "https://safetech.ge/logo.png",
      },
    },

    datePublished: post.created_at,
    dateModified: post.updated_at || post.created_at,

    mainEntityOfPage: {
      "@type": "WebPage",
      "@id": url,
    },

    inLanguage: "ka-GE",
  };
}

/* =========================
   🔥 BREADCRUMB SCHEMA
========================= */
function buildBreadcrumbSchema(post, url) {
const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;


  const items = [
    {
      "@type": "ListItem",
      position: 1,
      name: "მთავარი",
      item: BASE_URL,
    },
    {
      "@type": "ListItem",
      position: 2,
      name: "ბლოგი",
      item: `${BASE_URL}/blog`,
    },
  ];

  if (post.category?.slug) {
    items.push({
      "@type": "ListItem",
      position: 3,
      name: post.category.name,
      item: `${BASE_URL}/blog/category/${post.category.slug}`,
    });
  }

  items.push({
    "@type": "ListItem",
    position: items.length + 1,
    name: post.title,
    item: url,
  });

  return {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: items,
  };
}

/* =========================
   🔥 FAQ SCHEMA
========================= */
function buildFaqSchema(post) {
  if (!post.faq || !Array.isArray(post.faq)) return null;

  return {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    mainEntity: post.faq.map((item) => ({
      "@type": "Question",
      name: item.question,
      acceptedAnswer: {
        "@type": "Answer",
        text: item.answer,
      },
    })),
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
  const breadcrumbSchema = buildBreadcrumbSchema(post, url);
  const faqSchema = buildFaqSchema(post);

  /* 🔥 INTERNAL LINK MAP */
const keywordMap = await getSeoLinks();

  return (
    <main className="py-20 bg-[#F8FAFC]">

      {/* 🔥 SCHEMAS */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
      />

      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />

      {faqSchema && (
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
        />
      )}

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
           dangerouslySetInnerHTML={{
    __html: injectInternalLinks(post.excerpt, keywordMap),
  }}
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
__html: injectInternalLinks(section.content, keywordMap),                }}
              />
            </div>
          ))}
        </div>

        <SEOSection
          title="დამატებითი ინფორმაცია"
          paragraphs={post.seo?.content || [post.excerpt]}
          links={[]}
        />

      </article>
    </main>
  );
}