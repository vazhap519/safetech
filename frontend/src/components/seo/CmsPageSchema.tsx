import type { ReactNode } from "react";

import JsonLd from "@/components/seo/JsonLd";
import { getBackendSeoPage } from "@/lib/backend";
import { getCurrentLocale } from "@/lib/locale-server";

export default async function CmsPageSchema({
    pageKey,
    fallback,
}: {
    pageKey: string;
    fallback?: ReactNode;
}) {
    const locale = await getCurrentLocale();
    const seo = await getBackendSeoPage(pageKey, locale);

    if (seo?.schemaOverride) {
        return <JsonLd data={seo.schemaOverride} />;
    }

    if (fallback) {
        return fallback;
    }

    return seo?.schema ? <JsonLd data={seo.schema} /> : null;
}
