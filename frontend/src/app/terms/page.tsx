import type { Metadata } from "next";

import LegalPage from "@/components/pages/LegalPage";
import { getBackendPage } from "@/lib/backend";
import { getLegalPageFallback } from "@/lib/legal-page-fallback";
import { createMetadata, withSiteTitle } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export async function generateMetadata(): Promise<Metadata> {
    const [{ branding, locale, seo: siteSeo }, backendPage] = await Promise.all([
        getSiteSettings(),
        getBackendPage("terms"),
    ]);
    const page = backendPage ?? getLegalPageFallback("terms", locale);

    if (!backendPage) {
        return {
            title: withSiteTitle(page.title, branding.siteName),
            description: page.excerpt || page.content,
            robots: { index: false, follow: false },
        };
    }

    return createMetadata({
        title: page.seo?.title || page.title,
        description: page.seo?.description || page.excerpt || page.content,
        path: "/terms",
        locale,
        keywords: page.seo?.keywords || siteSeo.defaultKeywords,
        siteName: branding.siteName,
        noindex: Boolean(page.seo?.noindex),
        robotsIndex: siteSeo.robotsIndex,
        robotsFollow: siteSeo.robotsFollow,
    });
}

export default async function TermsPage() {
    const [{ locale }, backendPage] = await Promise.all([
        getSiteSettings(),
        getBackendPage("terms"),
    ]);
    const page = backendPage ?? getLegalPageFallback("terms", locale);

    return <LegalPage canonicalPath="/terms" locale={locale} page={page} />;
}
