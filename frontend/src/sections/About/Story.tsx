import StoryComponentsImage from "@/components/About/StoryComponents/StoryComponentsImage";
import StoryComponentsTypography from "@/components/About/StoryComponents/StoryComponentsTypography";
import { getPageImages } from "@/lib/page-images";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText } from "@/lib/translations";

const storyKeys = [
    "about.story.title",
    "about.story.paragraph.0",
    "about.story.paragraph.1",
];

export default async function StorySection() {
    const [{ locale, translations }, { aboutStory }] = await Promise.all([
        getSiteSettings(),
        getPageImages(),
    ]);

    if (!hasTranslatedText(translations, storyKeys, locale)) {
        return null;
    }

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div
                className={`mx-auto grid max-w-container-max grid-cols-1 items-center gap-unit-xl px-margin-desktop ${
                    aboutStory ? "md:grid-cols-2" : ""
                }`}
            >
                <div className={aboutStory ? "" : "mx-auto max-w-4xl"}>
                    <StoryComponentsTypography />
                </div>
                {aboutStory ? <StoryComponentsImage /> : null}
            </div>
        </section>
    );
}
