import { notFound } from "next/navigation";
import Image from "next/image";
import { getBaseUrl } from "@/lib/config";
import { getProject, getRelatedProjects, getSettings } from "@/lib/datafetch";
import Share from "../../components/Share";
import RelatedProjects from "../../components/projects/RelatedProjects";

export const dynamic = "force-dynamic";

const DEFAULT_IMAGE = "/services/1.jpg";

const absoluteImage = (image) => {
  if (!image) return `${getBaseUrl()}${DEFAULT_IMAGE}`;
  if (image.startsWith("http")) return image;
  return `${getBaseUrl()}${image.startsWith("/") ? image : `/${image}`}`;
};

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
        description: "Safetech პროექტები",
      };
    }

    const project = res.data;
    const seo = project?.seo || {};
    const title = seo.title || project.title;
    const description = seo.description || project.excerpt;
    const image = absoluteImage(project.image || DEFAULT_IMAGE);
    const url = `${getBaseUrl()}/projects/${slug}`;

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
      title: "პროექტები",
      description: "Safetech პროექტები",
    };
  }
}

function buildSchema(project, url) {
  return {
    "@context": "https://schema.org",
    "@type": "CreativeWork",
    "@id": `${url}#project`,
    name: project.title,
    description: project.excerpt,
    image: absoluteImage(project.image),
    author: {
      "@type": "Organization",
      name: "Safetech",
      url: getBaseUrl(),
    },
    publisher: {
      "@type": "Organization",
      name: "Safetech",
      url: getBaseUrl(),
    },
    datePublished: project.published_at || undefined,
    mainEntityOfPage: {
      "@type": "WebPage",
      "@id": url,
    },
    inLanguage: "ka-GE",
  };
}

export default async function ProjectDetail({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const [res, settings, relatedRes] = await Promise.all([
    getProject(slug),
    getSettings().catch(() => null),
    getRelatedProjects(slug).catch(() => null),
  ]);

  if (!res || res.error || !res.data) {
    return notFound();
  }

  const project = res.data;
  const url = `${getBaseUrl()}/projects/${project.slug}`;
  const schema = buildSchema(project, url);
  const related = relatedRes?.data || [];

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(schema) }}
      />

      <article className="max-w-4xl mx-auto px-4">
        {project.category?.name && (
          <span className="inline-block mb-3 text-xs bg-[#00C2A8]/10 text-[#00C2A8] px-3 py-1 rounded-full">
            {project.category.name}
          </span>
        )}

        <h1 className="text-4xl font-bold text-[#0B3C5D]">{project.title}</h1>

        <Image
          src={absoluteImage(project.image)}
          alt={project.title}
          width={1000}
          height={600}
          className="mt-6 rounded-xl w-full"
        />

        {project.excerpt && (
          <p className="mt-6 text-lg text-gray-600">{project.excerpt}</p>
        )}

        <Share data={settings?.share ?? {}} url={url} />

        {project.content && (
          <div
            className="prose max-w-none mt-10"
            dangerouslySetInnerHTML={{ __html: project.content }}
          />
        )}

        {project.gallery?.length > 0 && (
          <div className="mt-14">
            <h2 className="text-2xl font-bold text-[#0B3C5D] mb-4">
              გალერეა
            </h2>

            <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
              {project.gallery.map((img, i) => (
                <a key={i} href={img.url} target="_blank" rel="noreferrer">
                  <Image
                    src={absoluteImage(img.thumb || img.url)}
                    alt={`${project.title} ${i + 1}`}
                    width={400}
                    height={300}
                    className="rounded-xl hover:scale-105 transition"
                  />
                </a>
              ))}
            </div>
          </div>
        )}

        {project.video_url && (
          <div className="mt-14">
            <h2 className="text-2xl font-bold text-[#0B3C5D] mb-4">ვიდეო</h2>

            <div className="overflow-hidden rounded-2xl shadow">
              <iframe
                src={project.video_url}
                title={`${project.title} ვიდეო`}
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
