import Image from "@/components/ui/Image";
import { getPageImages } from "@/lib/page-images";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function StoryComponentsImage() {
    const [{ locale, translations }, { aboutStory }] = await Promise.all([
        getSiteSettings(),
        getPageImages(),
    ]);
    const imageAlt = translateText(
        translations,
        "about.story.imageAlt",
        locale,
        null,
    );

    if (!aboutStory) return null;

    return (
        <div className="group relative">
            <div className="absolute -inset-4 rounded-3xl bg-primary/20 blur-2xl transition-all duration-500 group-hover:bg-primary/30" />
            <Image
                alt={imageAlt}
                className="h-[320px] grayscale-[0.2] transition-all duration-700 group-hover:grayscale-0 sm:h-[420px] lg:h-[500px]"
                sizes="(max-width: 768px) 100vw, 50vw"
                src={aboutStory}
                variant="contact-support"
            />
        </div>
    );
}
