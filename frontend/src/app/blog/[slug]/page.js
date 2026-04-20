import { notFound } from "next/navigation";
import Image from "next/image";
import { getBaseUrl } from "@/lib/config";
import { getBlogPost, getSettings } from "@/lib/datafetch";
import { getSeoLinks } from "@/lib/getSeoLinks";
import { injectInternalLinks } from "@/lib/internalLinks";
import SEOSection from "@/app/components/SEOSection";
import Share from "../../components/Share";

export const revalidate = 300;

const DEFAULT_IMAGE = "/services/1.jpg";

const absoluteImage = (image) => {
  if (!image) return `${getBaseUrl()}${DEFAULT_IMAGE}`;
  if (image.startsWith("http")) return image;
  if (image.startsWith("/storage") || image.startsWith("/uploads")) {
    return `${process.env.NEXT_PUBLIC_API_URL?.replace(/\/api$/, "")}${image}`;
  }
  return `${getBaseUrl()}${image.startsWith("/") ? image : `/${image}`}`;
};

export async function generateMetadata({ params }) {
  const { slug } = await params;

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
        title: "სტატია ვერ მოიძებნა",
        description: "Safetech ბლოგი",
      };
    }

    const post = res.data;
    const seo = post?.seo?.meta || {};
    const title = seo.title || post.title;
    const description = seo.description || post.excerpt;
    const image = absoluteImage(seo.image || post.image);
    const url = seo.canonical || `${getBaseUrl()}/blog/${slug}`;

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
        images: [{ url: image, width: 1200, height: 630, alt: title }],
        locale: "ka_GE",
        siteName: "Safetech",
      },
      twitter: {
        card: "summary_large_image",
        title,
        description,
        images: [image],
      },
      robots: {
        index: !seo.noindex,
        follow: true,
      },
    };
  } catch {
    return {
      title: "ბლოგი",
      description: "Safetech ბლოგი",
    };
  }
}

function buildArticleSchema(post, url) {
  const seo = post?.seo?.meta || {};

  return {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "@id": `${url}#article`,
    headline: seo.title || post.title,
    description: seo.description || post.excerpt,
    image: {
      "@type": "ImageObject",
      url: absoluteImage(seo.image || post.image),
      width: 1200,
      height: 630,
    },
    author: {
      "@type": "Organization",
      name: post.author?.name || "Safetech",
    },
    publisher: {
      "@type": "Organization",
      name: "Safetech",
      url: getBaseUrl(),
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

function buildBreadcrumbSchema(post, url) {
  const baseUrl = getBaseUrl();
  const items = [
    {
      "@type": "ListItem",
      position: 1,
      name: "მთავარი",
      item: baseUrl,
    },
    {
      "@type": "ListItem",
      position: 2,
      name: "ბლოგი",
      item: `${baseUrl}/blog`,
    },
  ];

  if (post.category?.slug) {
    items.push({
      "@type": "ListItem",
      position: 3,
      name: post.category.name,
      item: `${baseUrl}/blog/category/${post.category.slug}`,
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

function buildFaqSchema(post) {
  if (!Array.isArray(post.faq) || post.faq.length === 0) return null;

  const mainEntity = post.faq
    .filter((item) => item?.question && item?.answer)
    .map((item) => ({
      "@type": "Question",
      name: item.question,
      acceptedAnswer: {
        "@type": "Answer",
        text: item.answer,
      },
    }));

  if (mainEntity.length === 0) return null;

  return {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    mainEntity,
  };
}

export default async function BlogDetailPage({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const [res, settings, keywordMap] = await Promise.all([
    getBlogPost(slug),
    getSettings().catch(() => null),
    getSeoLinks(),
  ]);

  if (!res || res.error || !res.data) {
    return notFound();
  }

  const post = res.data;
  const url = post?.seo?.meta?.canonical || `${getBaseUrl()}/blog/${post.slug}`;
  const customSchema = post?.seo?.schema;
  const primarySchemas = customSchema
    ? (Array.isArray(customSchema) ? customSchema : [customSchema])
    : [buildArticleSchema(post, url)];
  const schemas = [
    ...primarySchemas,
    buildBreadcrumbSchema(post, url),
    buildFaqSchema(post),
  ].filter(Boolean);
  const links = Array.isArray(keywordMap) ? keywordMap : [];

  return (
    <main className="py-20 bg-[#F8FAFC]">
      {schemas.map((schema, i) => (
        <script
          key={`blog-schema-${i}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
        />
      ))}

      <article className="max-w-4xl mx-auto px-4">
        <h1 className="text-4xl font-bold text-[#0B3C5D]">{post.title}</h1>

        <Image
          src={absoluteImage(post.image)}
          alt={post.title}
          width={1200}
          height={600}
          className="mt-6 rounded-xl w-full"
        />

        {post.excerpt && (
          <p
            className="mt-6 text-lg text-gray-600"
            dangerouslySetInnerHTML={{
              __html: injectInternalLinks(post.excerpt, links),
            }}
          />
        )}

        <Share data={res?.share ?? settings?.share ?? {}} url={url} />

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
                  __html: injectInternalLinks(section.content || "", links),
                }}
              />
            </div>
          ))}
        </div>

        <SEOSection
          title="დამატებითი ინფორმაცია"
          paragraphs={post.seo?.content || [post.excerpt].filter(Boolean)}
          links={[]}
        />
      </article>
    </main>
  );
}
