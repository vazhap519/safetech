import type { Metadata } from "next";

import LegalPage from "@/components/pages/LegalPage";
import { getBackendPage } from "@/lib/backend";
import { getLegalPageFallback } from "@/lib/legal-page-fallback";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";

export async function generateMetadata(): Promise<Metadata> {
    const [{ branding, locale, seo: siteSeo }, backendPage] = await Promise.all([
        getSiteSettings(),
        getBackendPage("privacy"),
    ]);
    const page = backendPage ?? getLegalPageFallback("privacy", locale);

    return createMetadata({
        title: page.seo?.title || page.title,
        description: page.seo?.description || page.excerpt || page.content,
        path: "/privacy",
        locale,
        keywords: page.seo?.keywords || siteSeo.defaultKeywords,
        siteName: branding.siteName,
        noindex: !backendPage || Boolean(page.seo?.noindex),
        robotsIndex: siteSeo.robotsIndex,
        robotsFollow: backendPage ? siteSeo.robotsFollow : false,
    });
}

export default async function PrivacyPage() {
    const [{ locale }, backendPage] = await Promise.all([
        getSiteSettings(),
        getBackendPage("privacy"),
    ]);
    const page = backendPage ?? getLegalPageFallback("privacy", locale);

    return <LegalPage canonicalPath="/privacy" locale={locale} page={page} />;
}
