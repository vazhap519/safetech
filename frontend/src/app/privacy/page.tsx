import PrivacyPageContent from "@/components/pages/PrivacyPageContent";
import CorePageFallback from "@/components/seo/CorePageFallback";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.privacy);
}

export default function PrivacyPage() {
    return (
        <>
            <CmsPageSchema pageKey="privacy" />
            <CorePageFallback pageKey="privacy" />
            <PrivacyPageContent />
        </>
    );
}
