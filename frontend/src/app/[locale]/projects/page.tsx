import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { normalizeLocale } from "@/lib/locales";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export { default } from "@/app/projects/page";

type LocalizedPageProps = {
    params: Promise<{ locale: string }>;
};

export async function generateMetadata({ params }: LocalizedPageProps) {
    const { locale } = await params;

    return createCmsPageMetadata(
        PAGE_SEO_PRESETS.projects,
        normalizeLocale(locale),
    );
}
