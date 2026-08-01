import "server-only";

import { getBackendSeoPage } from "@/lib/backend";
import { getCurrentLocale } from "@/lib/locale-server";
import type { PageSeoPreset } from "@/lib/page-seo-presets";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export async function createCmsPageMetadata(preset: PageSeoPreset) {
    const locale = await getCurrentLocale();
    const [settings, cmsSeo] = await Promise.all([
        getSiteSettings(),
        getBackendSeoPage(preset.key, locale),
    ]);
    const translationKey = preset.translationKey ?? preset.key;
    const translatedTitle = translateText(
        settings.translations,
        `meta.${translationKey}.title`,
        locale,
        preset.title,
    );
    const translatedDescription = translateText(
        settings.translations,
        `meta.${translationKey}.description`,
        locale,
        preset.description,
    );

    return createMetadata({
        title: cmsSeo?.title || translatedTitle,
        description: cmsSeo?.description || translatedDescription,
        path: preset.path,
        locale,
        keywords: cmsSeo?.keywords?.length
            ? cmsSeo.keywords
            : settings.seo.defaultKeywords.length
              ? settings.seo.defaultKeywords
              : preset.keywords,
        image:
            cmsSeo?.og?.image ||
            cmsSeo?.share_image ||
            settings.branding.defaultImage || undefined,
        siteName: settings.branding.siteName,
        type: preset.type,
        canonical: cmsSeo?.canonical,
        ogTitle: cmsSeo?.og?.title,
        ogDescription: cmsSeo?.og?.description,
        noindex: Boolean(cmsSeo?.noindex),
        robotsIndex: settings.seo.robotsIndex,
        robotsFollow: settings.seo.robotsFollow,
    });
}
