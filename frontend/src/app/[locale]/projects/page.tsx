import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { normalizeLocale } from "@/lib/locales";
import { parsePageNumber, withPaginationMetadata } from "@/lib/pagination";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export { default } from "@/app/projects/page";

type LocalizedPageProps = {
    params: Promise<{ locale: string }>;
    searchParams?: Promise<{ page?: string }>;
};

export async function generateMetadata({ params, searchParams }: LocalizedPageProps) {
    const { locale } = await params;
    const normalizedLocale = normalizeLocale(locale);
    const page = parsePageNumber((await searchParams)?.page) ?? 1;
    const metadata = await createCmsPageMetadata(
        PAGE_SEO_PRESETS.projects,
        normalizedLocale,
    );

    return withPaginationMetadata(
        metadata,
        "/projects",
        page,
        normalizedLocale,
    );
}
