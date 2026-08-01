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
    const schema = seo?.schemaOverride || seo?.schema;

    return schema ? <JsonLd data={schema} /> : fallback;
}
