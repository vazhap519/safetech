import "server-only";

import { getBackendSeoPage } from "@/lib/backend";
import { getCurrentLocale } from "@/lib/locale-server";
import { DEFAULT_LOCALE, type Locale } from "@/lib/locales";
import { getPageImages, type PageImages } from "@/lib/page-images";
import type { PageSeoPreset } from "@/lib/page-seo-presets";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const PAGE_SOCIAL_IMAGE_KEYS: Partial<Record<string, keyof PageImages>> = {
    home: "homeHero",
    about: "aboutStory",
    services: "servicesHero",
    projects: "projectsHero",
    contact: "contactIntro",
};

function isLegacyGenericSocialImage(value?: string | null) {
    if (!value) return false;

    try {
        return new URL(value, "https://safetech.ge").pathname === "/services/1.jpg";
    } catch {
        return false;
    }
}

export async function createCmsPageMetadata(
    preset: PageSeoPreset,
    explicitLocale?: Locale,
) {
    const locale = explicitLocale ?? (await getCurrentLocale());
    const [settings, cmsSeo, pageImages] = await Promise.all([
        getSiteSettings(),
        getBackendSeoPage(preset.key, locale),
        getPageImages(),
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
    const configuredSocialImage =
        cmsSeo?.og?.image || cmsSeo?.share_image || null;
    const pageImageKey = PAGE_SOCIAL_IMAGE_KEYS[preset.key];
    const pageSocialImage = pageImageKey ? pageImages[pageImageKey] : null;
    const socialImage =
        pageSocialImage &&
        (!configuredSocialImage ||
            isLegacyGenericSocialImage(configuredSocialImage))
            ? pageSocialImage
            : configuredSocialImage;

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
            socialImage ||
            settings.branding.defaultImage || undefined,
        siteName: settings.branding.siteName,
        type: preset.type,
        canonical: locale === DEFAULT_LOCALE ? cmsSeo?.canonical : undefined,
        ogTitle: cmsSeo?.og?.title,
        ogDescription: cmsSeo?.og?.description,
        noindex: Boolean(preset.noindex || cmsSeo?.noindex),
        robotsIndex: settings.seo.robotsIndex,
        robotsFollow: settings.seo.robotsFollow,
    });
}
