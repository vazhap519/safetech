import type { Metadata } from "next";
import { notFound } from "next/navigation";

import DynamicPage from "@/components/pages/DynamicPage";
import { getBackendPage } from "@/lib/backend";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

type DynamicPageProps = {
    params: Promise<{ slug: string }>;
};

export async function generateMetadata({ params }: DynamicPageProps): Promise<Metadata> {
    const { slug } = await params;
    const [{ branding, locale, seo: siteSeo }, page] = await Promise.all([
        getSiteSettings(),
        getBackendPage(slug),
    ]);

    if (!page) {
        return { title: withSiteTitle("Page not found", branding.siteName), robots: { index: false, follow: false } };
    }

    return createMetadata({
        title: page.seo?.title || page.title,
        description: page.seo?.description || page.excerpt || page.content,
        path: `/pages/${page.slug}`,
        locale,
        keywords: page.seo?.keywords || siteSeo.defaultKeywords,
        image: page.seo?.image || page.coverImage || undefined,
        siteName: branding.siteName,
        noindex: Boolean(page.seo?.noindex),
        robotsIndex: siteSeo.robotsIndex,
        robotsFollow: siteSeo.robotsFollow,
    });
}

export default async function DynamicPageRoute({ params }: DynamicPageProps) {
    const { slug } = await params;
    const [{ locale }, page] = await Promise.all([getSiteSettings(), getBackendPage(slug)]);

    if (!page) notFound();

    return <DynamicPage locale={locale} page={page} />;
}
