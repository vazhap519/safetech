import StoryComponentsImage from "@/components/About/StoryComponents/StoryComponentsImage";
import StoryComponentsTypography from "@/components/About/StoryComponents/StoryComponentsTypography";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText } from "@/lib/translations";

const storyKeys = [
    "about.story.title",
    "about.story.paragraph.0",
    "about.story.paragraph.1",
];

export default async function StorySection() {
    const { locale, translations } = await getSiteSettings();

    if (!hasTranslatedText(translations, storyKeys, locale)) {
        return null;
    }

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto grid max-w-container-max grid-cols-1 items-center gap-unit-xl px-margin-desktop md:grid-cols-2">
                <StoryComponentsTypography />
                <StoryComponentsImage />
            </div>
        </section>
    );
}
