import { notFound } from "next/navigation";
import { getProject, getSettings } from "@/lib/datafetch";
import Share from "../../components/Share";
import { getCurrentUrl } from "@/lib/getUrl";
import Image from "next/image";
import { getRelatedProjects } from "@/lib/datafetch";
import RelatedProjects from "../../components/projects/RelatedProjects";
import MagneticCard from "../../components/projects/MagneticCard"
export const dynamic = "force-dynamic";

const DEFAULT_IMAGE = "/placeholder.jpg";

/* =========================
   🔥 METADATA
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) {
    return {
      title: "პროექტები",
      description: "Safetech პროექტები",
    };
  }

  try {
    const res = await getProject(slug);

    if (!res || res.error || !res.data) {
      return {
        title: "პროექტი ვერ მოიძებნა",
        description: "Safetech",
      };
    }

    const project = res.data;
    const seo = project?.seo || {};

    const title = seo.title || project.title;
    const description = seo.description || project.excerpt;

    const image =
      project.image ||
      DEFAULT_IMAGE;

    const url = await getCurrentUrl(`/projects/${slug}`);

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
            alt: title,
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
      title: "პროექტები",
      description: "Safetech",
    };
  }
}

/* =========================
   🔥 SCHEMA
========================= */
function buildSchema(project, url) {
  return {
    "@context": "https://schema.org",
    "@type": "CreativeWork",

    name: project.title,
    description: project.excerpt,
    image: project.image || DEFAULT_IMAGE,

    author: {
      "@type": "Organization",
      name: "Safetech",
    },

    publisher: {
      "@type": "Organization",
      name: "Safetech",
    },

    datePublished:
      project.published_at || new Date().toISOString(),

    mainEntityOfPage: url,
  };
}

/* =========================
   PAGE
========================= */
export default async function ProjectDetail({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const res = await getProject(slug);

  if (!res || res.error || !res.data) {
    return notFound();
  }

  const project = res.data;

  /* 🔥 SETTINGS (როგორც blog-ში) */
  const settings = await getSettings();

  const url = await getCurrentUrl(`/projects/${project.slug}`);
  const schema = buildSchema(project, url);
const relatedRes = await getRelatedProjects(slug);
const related = relatedRes?.data || [];
  return (
    <main className="py-20 bg-[#F8FAFC]">

      {/* 🔥 SCHEMA */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{
          __html: JSON.stringify(schema),
        }}
      />

      <article className="max-w-4xl mx-auto px-4">

        {/* CATEGORY */}
        {project.category?.name && (
          <span className="inline-block mb-3 text-xs bg-[#00C2A8]/10 text-[#00C2A8] px-3 py-1 rounded-full">
            {project.category.name}
          </span>
        )}

        {/* TITLE */}
        <h1 className="text-4xl font-bold text-[#0B3C5D]">
          {project.title}
        </h1>

        {/* IMAGE */}
        <Image
          src={project.image || DEFAULT_IMAGE}
          alt={project.title}
          width={1000}
          height={600}
          className="mt-6 rounded-xl w-full"
        />

        {/* EXCERPT */}
        {project.excerpt && (
          <p className="mt-6 text-lg text-gray-600">
            {project.excerpt}
          </p>
        )}

        {/* SHARE (FIXED LIKE BLOG) */}
        <Share data={settings?.share ?? {}} url={url} />

        {/* CONTENT */}
        {project.content && (
          <div
            className="prose max-w-none mt-10"
            dangerouslySetInnerHTML={{
              __html: project.content,
            }}
          />
        )}

        {/* GALLERY */}
        {project.gallery?.length > 0 && (
          <div className="mt-14">
            <h2 className="text-2xl font-bold text-[#0B3C5D] mb-4">
              გალერეა
            </h2>

            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
              {project.gallery.map((img, i) => (
                <a key={i} href={img.url} target="_blank">
                  <Image
                    src={img.thumb}
                    alt=""
                    width={400}
                    height={300}
                    className="rounded-xl hover:scale-105 transition"
                  />
                </a>
              ))}
            </div>
          </div>
        )}

        {/* VIDEO */}
        {project.video_url && (
          <div className="mt-14">
            <h2 className="text-2xl font-bold text-[#0B3C5D] mb-4">
              ვიდეო
            </h2>

            <div className="overflow-hidden rounded-2xl shadow">
              <iframe
                src={project.video_url}
                className="w-full h-[400px]"
                allowFullScreen
              />
            </div>
          </div>
        )}

      </article>
      <RelatedProjects projects={related} />
    </main>
  );
}