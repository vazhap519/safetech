import type { Metadata } from "next";
import { notFound } from "next/navigation";

import LegalPage from "@/components/pages/LegalPage";
import { getBackendPage } from "@/lib/backend";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export async function generateMetadata(): Promise<Metadata> {
    const [{ branding, locale, seo: siteSeo }, page] = await Promise.all([
        getSiteSettings(),
        getBackendPage("privacy"),
    ]);

    if (!page) {
        return {
            title: withSiteTitle("Privacy Policy", branding.siteName),
            robots: { index: false, follow: false },
        };
    }

    return createMetadata({
        title: page.seo?.title || page.title,
        description: page.seo?.description || page.excerpt || page.content,
        path: "/privacy",
        locale,
        keywords: page.seo?.keywords || siteSeo.defaultKeywords,
        siteName: branding.siteName,
        noindex: Boolean(page.seo?.noindex),
        robotsIndex: siteSeo.robotsIndex,
        robotsFollow: siteSeo.robotsFollow,
    });
}

export default async function PrivacyPage() {
    const [{ locale }, page] = await Promise.all([
        getSiteSettings(),
        getBackendPage("privacy"),
    ]);

    if (!page) notFound();

    return <LegalPage canonicalPath="/privacy" locale={locale} page={page} />;
}
