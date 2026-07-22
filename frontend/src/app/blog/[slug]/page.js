import Image from "next/image";
import Link from "next/link";
import { notFound } from "next/navigation";

import BlogPostSchema from "@/components/seo/BlogPostSchema";
import JsonLd from "@/components/seo/JsonLd";
import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
import Icon from "@/components/ui/Icon";
import { getBlogPost } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";
import { createMetadata, localizeHref, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const DEFAULT_IMAGE = "/brand-preview.svg";

export async function generateMetadata({ params }) {
  const { slug } = await params;

  if (!slug) return {};
  const [locale, settings] = await Promise.all([
    getCurrentLocale(),
    getSiteSettings(),
  ]);
  const response = await getBlogPost(slug, { locale });
  const post = response?.error ? null : response?.data;

  if (!post) {
    const notFoundTitle = translateText(
      settings.translations,
      "meta.blog.notFound",
      locale,
      {
        ka: "სტატია ვერ მოიძებნა",
        en: "Article not found",
        ru: "Статья не найдена",
      },
    );

    return {
      title: withSiteTitle(notFoundTitle, settings.branding.siteName),
      robots: { index: false, follow: false },
    };
  }

  return createMetadata({
    title: post.meta?.title || post.title,
    description: post.meta?.description || post.excerpt,
    path: `/blog/${post.slug}`,
    locale,
    keywords: post.meta?.keywords || [],
    image: post.meta?.image || post.image || settings.branding.defaultImage,
    siteName: settings.branding.siteName,
    type: "article",
    noindex: Boolean(post.meta?.noindex),
  });
}

export default async function BlogDetailPage({ params }) {
  const { slug } = await params;

  if (!slug) return notFound();

  const [locale, { translations }] = await Promise.all([
    getCurrentLocale(),
    getSiteSettings(),
  ]);
  const res = await getBlogPost(slug, { locale });
  const post = res?.error ? null : res?.data;

  if (!post) return notFound();

  const homeLabel = translateText(translations, "nav.home", locale, null);
  const blogLabel = translateText(translations, "blog.title", locale, null);
  const minReadLabel = translateText(translations, "blog.minRead", locale, null);
  const relatedLabel = translateText(translations, "blog.related", locale, null);
  const contentsLabel = translateText(translations, "blog.contents", locale, null);
  const sectionLabel = translateText(translations, "blog.section", locale, null);
  const detailsLabel = translateText(
    translations,
    "common.readMore",
    locale,
    { ka: "დეტალურად", en: "View details", ru: "Подробнее" },
  );

  return (
    <div className="bg-background px-4 pb-20 pt-28 sm:pt-32">
      {post.meta?.schema ? (
        <JsonLd data={post.meta.schema} />
      ) : (
        <BlogPostSchema
          author={post.meta?.author || post.author?.name}
          description={post.meta?.description || post.excerpt || post.title}
          image={post.meta?.image || post.image || DEFAULT_IMAGE}
          locale={locale}
          publishedAt={post.published_at}
          slug={post.slug}
          title={post.meta?.title || post.title}
          updatedAt={post.updated_at}
        />
      )}
      <div className="mx-auto grid max-w-6xl gap-10 px-4 md:grid-cols-3">
        <article className="md:col-span-2">
          {homeLabel || blogLabel ? (
            <nav
              className="mb-4 text-sm text-on-surface-variant"
              aria-label={translateText(
                translations,
                "blog.breadcrumb",
                locale,
                null,
              )}
            >
              {homeLabel ? (
                <>
                  <Link
                    href={localizeHref("/", locale)}
                    className="inline-flex min-h-10 items-center hover:text-secondary"
                  >
                    {homeLabel}
                  </Link>{" "}
                </>
              ) : null}
              {homeLabel && blogLabel ? "/ " : null}
              {blogLabel ? (
                <>
                  <Link
                    href={localizeHref("/blog", locale)}
                    className="inline-flex min-h-10 items-center hover:text-secondary"
                  >
                    {blogLabel}
                  </Link>{" "}
                </>
              ) : null}
              {(homeLabel || blogLabel) && post.title ? " / " : null}
              {post.title ? (
                <span className="text-on-surface">{post.title}</span>
              ) : null}
            </nav>
          ) : null}

          <div className="group relative h-72 overflow-hidden rounded-lg border border-outline-variant/20 shadow-card">
            <Image
              src={post.image || DEFAULT_IMAGE}
              alt={post.title || ""}
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
                {post.reading_time}
                {minReadLabel ? ` ${minReadLabel}` : null}
              </span>
            ) : null}
            {post.category?.name ? (
              <span className="rounded-full border border-secondary/20 bg-secondary/10 px-3 py-1 text-secondary">
                {post.category.name}
              </span>
            ) : null}
          </div>

          {post.title ? (
            <h1 className="mt-4 text-3xl font-bold text-on-surface md:text-4xl">
              {post.title}
            </h1>
          ) : null}

          {post.excerpt ? (
            <p className="mt-4 text-lg leading-relaxed text-on-surface-variant">
              {post.excerpt}
            </p>
          ) : null}

          {post.sections?.length ? (
            <div className="mt-10 space-y-6 text-[17px] leading-relaxed text-on-surface-variant">
              {post.sections.map((section, index) => (
                <section
                  key={section.id ?? index}
                  id={`section-${index}`}
                  className="rounded-lg border border-outline-variant/20 bg-surface-container-low p-5 shadow-card"
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
          ) : null}

          {post.author ? (
            <div className="mt-12 flex items-center gap-4 rounded-lg border border-outline-variant/20 bg-surface-container-low p-6 shadow-card">
              <div className="relative h-14 w-14 overflow-hidden rounded-full border border-outline-variant/20">
                <Image
                  src={post.author.avatar || DEFAULT_IMAGE}
                  alt={post.author.name || ""}
                  fill
                  sizes="56px"
                  className="object-cover"
                />
              </div>
              {post.author.name ? (
                <p className="font-semibold text-on-surface">{post.author.name}</p>
              ) : null}
            </div>
          ) : null}

          {post.related?.length ? (
            <div className="mt-16">
              {relatedLabel ? (
                <h2 className="mb-6 text-2xl font-bold text-on-surface">
                  {relatedLabel}
                </h2>
              ) : null}

              <div className="grid gap-6 md:grid-cols-3">
                {post.related.map((item) => (
                  <Link
                    className="block h-full"
                    key={item.slug}
                    href={localizeHref(`/blog/${item.slug}`, locale)}
                  >
                    <article className="h-full overflow-hidden rounded-lg border border-outline-variant/20 bg-surface-container-low shadow-card transition hover:-translate-y-1 hover:border-secondary/30">
                      <div className="relative h-32 w-full">
                        <Image
                          src={item.image || DEFAULT_IMAGE}
                          alt={item.title || ""}
                          fill
                          sizes="(max-width: 768px) 100vw, 240px"
                          className="object-cover"
                        />
                      </div>

                      {item.title ? (
                        <div className="p-4">
                          <h3 className="text-sm font-semibold text-on-surface">
                            {item.title}
                          </h3>
                          {detailsLabel ? (
                            <span className={`${CARD_ACTION_CLASS} mt-4`}>
                              {detailsLabel}
                              <Icon aria-hidden="true" name="arrow_forward" />
                            </span>
                          ) : null}
                        </div>
                      ) : null}
                    </article>
                  </Link>
                ))}
              </div>
            </div>
          ) : null}
        </article>

        {contentsLabel && post.sections?.length ? (
          <aside className="hidden md:block">
            <div className="sticky top-24 rounded-lg border border-outline-variant/20 bg-surface-container-low p-6 shadow-card">
              <h2 className="mb-4 font-semibold text-on-surface">
                {contentsLabel}
              </h2>

              <ul className="space-y-3 text-sm">
                {post.sections.map((section, index) => (
                  <li key={section.id ?? index}>
                    <a
                      href={`#section-${index}`}
                      className="inline-flex min-h-10 items-center text-on-surface-variant hover:text-secondary"
                    >
                      {section.title ||
                        (sectionLabel ? `${sectionLabel} ${index + 1}` : "")}
                    </a>
                  </li>
                ))}
              </ul>
            </div>
          </aside>
        ) : null}
      </div>
    </div>
  );
}
