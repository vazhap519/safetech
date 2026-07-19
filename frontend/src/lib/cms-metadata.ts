import "server-only";

import { getBackendSeoPage } from "@/lib/backend";
import type { PageSeoPreset } from "@/lib/page-seo-presets";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export async function createCmsPageMetadata(preset: PageSeoPreset) {
    const settings = await getSiteSettings();
    const cmsSeo = await getBackendSeoPage(preset.key, settings.locale);
    const translationKey = preset.translationKey ?? preset.key;
    const translatedTitle = translateText(
        settings.translations,
        `meta.${translationKey}.title`,
        settings.locale,
        preset.title,
    );
    const translatedDescription = translateText(
        settings.translations,
        `meta.${translationKey}.description`,
        settings.locale,
        preset.description,
    );

    return createMetadata({
        title: cmsSeo?.title || translatedTitle,
        description: cmsSeo?.description || translatedDescription,
        path: preset.path,
        locale: settings.locale,
        keywords: cmsSeo?.keywords?.length ? cmsSeo.keywords : preset.keywords,
        image:
            cmsSeo?.og?.image ||
            cmsSeo?.share_image ||
            settings.branding.defaultImage,
        siteName: settings.branding.siteName,
        type: preset.type,
        noindex: Boolean(cmsSeo?.noindex),
    });
}
