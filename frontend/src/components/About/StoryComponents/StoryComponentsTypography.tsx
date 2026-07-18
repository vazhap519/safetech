import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function StoryComponentsTypography() {
    const { locale, translations } = await getSiteSettings();
    const title = translateText(translations, "about.story.title", locale, null);
    const paragraphs = [
        translateText(translations, "about.story.paragraph.0", locale, null),
        translateText(translations, "about.story.paragraph.1", locale, null),
    ].filter(Boolean);

    if (!title && !paragraphs.length) return null;

    return (
        <div className="space-y-unit-lg">
            {title ? (
                <>
                    <Typography as="h2" variant="section-title">
                        {title}
                    </Typography>
                    <div className="h-1 w-20 bg-primary" />
                </>
            ) : null}

            {paragraphs.map((paragraph) => (
                <Typography as="p" key={paragraph} variant="section-description">
                    {paragraph}
                </Typography>
            ))}
        </div>
    );
}
