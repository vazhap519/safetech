import { getPrivacy } from "@/lib/datafetch";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { getCurrentLocale } from "@/lib/locale-server";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { notFound } from "next/navigation";

export async function generateMetadata() {
  return createCmsPageMetadata(PAGE_SEO_PRESETS.privacy);
}

export default async function PrivacyPage() {
  const locale = await getCurrentLocale();
  const privacy = await getPrivacy({ locale });

  if (!privacy?.title && !privacy?.highlight && !privacy?.content) {
    notFound();
  }

  return (
    <div className="bg-background pt-[76px] text-on-surface">
      <CmsPageSchema pageKey="privacy" />
      {privacy?.title || privacy?.highlight ? (
        <section className="border-b border-outline-variant/20 bg-surface-container-lowest py-12 md:py-16">
          <div className="mx-auto max-w-5xl px-5 text-center md:px-8">
            {privacy?.title ? (
              <h1 className="mb-4 text-[30px] font-semibold leading-[1.2] text-on-surface sm:text-[34px] md:text-[40px]">
                {privacy.title}
              </h1>
            ) : null}
            {privacy?.highlight ? (
              <p className="mx-auto max-w-2xl text-base leading-[1.7] text-on-surface-variant md:text-[17px]">
                {privacy.highlight}
              </p>
            ) : null}
          </div>
        </section>
      ) : null}

      {privacy?.content ? (
        <section className="mx-auto max-w-4xl px-5 py-12 md:px-8 md:py-16">
            <div
              className="legal-content"
              dangerouslySetInnerHTML={{ __html: privacy.content }}
            />
        </section>
      ) : null}
    </div>
  );
}
