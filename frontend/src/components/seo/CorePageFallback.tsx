import { getCurrentLocale } from "@/lib/locale-server";
import {
    PAGE_SEO_PRESETS,
    type PageSeoPreset,
    type PageSeoPresetKey,
} from "@/lib/page-seo-presets";
import { hasConfiguredPageHeading } from "@/lib/page-content";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function CorePageFallback({
    pageKey,
}: {
    pageKey: PageSeoPresetKey;
}) {
    const locale = await getCurrentLocale();
    const { translations } = await getSiteSettings();

    if (hasConfiguredPageHeading(translations, pageKey, locale)) {
        return null;
    }

    const preset = PAGE_SEO_PRESETS[pageKey] as PageSeoPreset;
    const translationKey = preset.translationKey ?? preset.key;
    const title = translateText(
        translations,
        `meta.${translationKey}.title`,
        locale,
        preset.title,
    );
    const description = translateText(
        translations,
        `meta.${translationKey}.description`,
        locale,
        preset.description,
    );

    return (
        <section className="mx-auto max-w-container-max px-5 pb-12 pt-32 text-center sm:px-6 lg:px-14">
            <h1 className="text-4xl font-bold tracking-tight text-on-background md:text-5xl">
                {title}
            </h1>
            <p className="mx-auto mt-5 max-w-3xl text-base leading-7 text-on-surface-variant md:text-lg">
                {description}
            </p>
        </section>
    );
}
