import type { ReactNode } from "react";

import JsonLd from "@/components/seo/JsonLd";
import { getBackendSeoPage } from "@/lib/backend";
import { hasConfiguredPageHeading } from "@/lib/page-content";
import { getSiteSettings } from "@/lib/site-settings";

export default async function CmsPageSchema({
    pageKey,
    fallback,
}: {
    pageKey: string;
    fallback?: ReactNode;
}) {
    const { locale, translations } = await getSiteSettings();

    if (
        pageKey !== "privacy" &&
        !hasConfiguredPageHeading(translations, pageKey, locale)
    ) {
        return null;
    }

    const seo = await getBackendSeoPage(pageKey, locale);

    const schema = seo?.schemaOverride || seo?.schema;

    return schema ? <JsonLd data={schema} /> : fallback;
}
