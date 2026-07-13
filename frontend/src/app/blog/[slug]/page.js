import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";

import { getBlogPost } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";
import { generateSeo } from "@/lib/seoEngine";

const DEFAULT_IMAGE = "/brand-preview.svg";

export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) return {};
  const locale = await getCurrentLocale();

  return generateSeo({
    type: "blog",
    slug,
    locale,
  });
}

export default async function BlogDetailPage({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const locale = await getCurrentLocale();
  const copy = {
    ka: {
      home: "მთავარი",
      blog: "ბლოგი",
      minRead: "წთ წაკითხვა",
      related: "მსგავსი სტატიები",
      contents: "სარჩევი",
      section: "სექცია",
      imageAlt: "ბლოგის ფოტო",
      authorAlt: "ავტორი",
      relatedAlt: "მსგავსი სტატია",
    },
    en: {
      home: "Home",
      blog: "Blog",
      minRead: "min read",
      related: "Related articles",
      contents: "Contents",
      section: "Section",
      imageAlt: "Blog image",
      authorAlt: "Author",
      relatedAlt: "Related article",
    },
    ru: {
      home: "Главная",
      blog: "Блог",
      minRead: "мин чтения",
      related: "Похожие статьи",
      contents: "Содержание",
      section: "Раздел",
      imageAlt: "Изображение блога",
      authorAlt: "Автор",
      relatedAlt: "Похожая статья",
    },
  }[locale];
  const res = await getBlogPost(slug, { locale });
  const post = res?.data;

  if (!post) return notFound();

  return (
    <main className="bg-background px-4 pb-20 pt-28 sm:pt-32">
      <div className="mx-auto grid max-w-6xl gap-10 px-4 md:grid-cols-3">
        <article className="md:col-span-2">
          <nav className="mb-4 text-sm text-on-surface-variant" aria-label="Breadcrumb">
            <Link href="/" className="inline-flex min-h-10 items-center hover:text-secondary">
              {copy.home}
            </Link>{" "}
            /{" "}
            <Link href="/blog" className="inline-flex min-h-10 items-center hover:text-secondary">
              {copy.blog}
            </Link>{" "}
            / <span className="text-on-surface">{post.title}</span>
          </nav>

          <div className="group relative h-72 overflow-hidden rounded-2xl border border-outline-variant/20 shadow-card">
            <Image
              src={post.image || DEFAULT_IMAGE}
              alt={post.title || copy.imageAlt}
              fill
              priority
              sizes="(max-width: 768px) 100vw, 768px"
              className="object-cover transition group-hover:scale-105"
            />
          </div>

          <div className="mt-6 flex flex-wrap gap-3 text-xs text-on-surface-variant">
            {post.published_year ? (
              <span className="rounded-full border border-outline-variant/20 bg-surface-container-low px-3 py-1">
                {post.published_year}
              </span>
            ) : null}
            {post.reading_time ? (
              <span className="rounded-full border border-outline-variant/20 bg-surface-container-low px-3 py-1">
                {post.reading_time} {copy.minRead}
              </span>
            ) : null}
            {post.category?.name ? (
              <span className="rounded-full border border-secondary/20 bg-secondary/10 px-3 py-1 text-secondary">
                {post.category.name}
              </span>
            ) : null}
          </div>

          <h1 className="mt-4 text-3xl font-bold text-on-surface md:text-4xl">
            {post.title}
          </h1>

          {post.excerpt ? (
            <p className="mt-4 text-lg leading-relaxed text-on-surface-variant">
              {post.excerpt}
            </p>
          ) : null}

          <div className="mt-10 space-y-6 text-[17px] leading-relaxed text-on-surface-variant">
            {post.sections?.map((section, index) => (
              <section
                key={section.id ?? index}
                id={`section-${index}`}
                className="rounded-2xl border border-outline-variant/20 bg-surface-container-low p-5 shadow-card"
              >
                {section.title ? (
                  <h2 className="mb-3 text-xl font-semibold text-on-surface">
                    {section.title}
                  </h2>
                ) : null}
                <div
                  className="[&_a]:text-secondary [&_p:not(:last-child)]:mb-4"
                  dangerouslySetInnerHTML={{
                    __html: section.content ?? "",
                  }}
                />
              </section>
            ))}
          </div>

          {post.author ? (
            <div className="mt-12 flex items-center gap-4 rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-card">
              <div className="relative h-14 w-14 overflow-hidden rounded-full border border-outline-variant/20">
                <Image
                  src={post.author.avatar || DEFAULT_IMAGE}
                  alt={post.author.name || copy.authorAlt}
                  fill
                  sizes="56px"
                  className="object-cover"
                />
              </div>
              <p className="font-semibold text-on-surface">{post.author.name}</p>
            </div>
          ) : null}

          {post.related?.length ? (
            <div className="mt-16">
              <h2 className="mb-6 text-2xl font-bold text-on-surface">
                {copy.related}
              </h2>

              <div className="grid gap-6 md:grid-cols-3">
                {post.related.map((item) => (
                  <Link className="block h-full" key={item.slug} href={`/blog/${item.slug}`}>
                    <article className="h-full overflow-hidden rounded-2xl border border-outline-variant/20 bg-surface-container-low shadow-card transition hover:-translate-y-1 hover:border-secondary/30">
                      <div className="relative h-32 w-full">
                        <Image
                          src={item.image || DEFAULT_IMAGE}
                          alt={item.title || copy.relatedAlt}
                          fill
                          sizes="(max-width: 768px) 100vw, 240px"
                          className="object-cover"
                        />
                      </div>

                      <div className="p-4">
                        <h3 className="text-sm font-semibold text-on-surface">
                          {item.title}
                        </h3>
                      </div>
                    </article>
                  </Link>
                ))}
              </div>
            </div>
          ) : null}
        </article>

        <aside className="hidden md:block">
          <div className="sticky top-24 rounded-2xl border border-outline-variant/20 bg-surface-container-low p-6 shadow-card">
            <h2 className="mb-4 font-semibold text-on-surface">{copy.contents}</h2>

            <ul className="space-y-3 text-sm">
              {post.sections?.map((section, index) => (
                <li key={section.id ?? index}>
                  <a
                    href={`#section-${index}`}
                    className="inline-flex min-h-10 items-center text-on-surface-variant hover:text-secondary"
                  >
                    {section.title || `${copy.section} ${index + 1}`}
                  </a>
                </li>
              ))}
            </ul>
          </div>
        </aside>
      </div>
    </main>
  );
}
