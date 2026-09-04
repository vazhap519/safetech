import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getPageImages } from "@/lib/page-images";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText, translateText } from "@/lib/translations";

const introKeys = [
    "contact.intro.title",
    "contact.intro.paragraph.0",
    "contact.intro.paragraph.1",
    "contact.intro.badge.0",
    "contact.intro.badge.1",
];

export default async function Intro() {
    const [{ locale, translations }, { contactIntro }] = await Promise.all([
        getSiteSettings(),
        getPageImages(),
    ]);

    if (!hasTranslatedText(translations, introKeys, locale)) {
        return null;
    }

    const title = translateText(translations, "contact.intro.title", locale, null);
    const paragraphs = [
        translateText(translations, "contact.intro.paragraph.0", locale, null),
        translateText(translations, "contact.intro.paragraph.1", locale, null),
    ].filter(Boolean);
    const badges = [
        {
            className: "bg-secondary shadow-[0_0_8px_#4cd7f6]",
            textClassName: "text-secondary",
            label: translateText(
                translations,
                "contact.intro.badge.0",
                locale,
                null,
            ),
        },
        {
            className: "bg-primary shadow-[0_0_8px_#b4c5ff]",
            textClassName: "text-primary",
            label: translateText(
                translations,
                "contact.intro.badge.1",
                locale,
                null,
            ),
        },
    ].filter((badge) => badge.label);
    const imageAlt = translateText(
        translations,
        "contact.intro.imageAlt",
        locale,
        null,
    );

    return (
        <section className="bg-background py-10 sm:py-12 lg:py-16">
            <div
                className={`container mx-auto grid max-w-container-max grid-cols-1 items-center gap-6 px-margin-desktop lg:gap-10 ${
                    contactIntro ? "md:grid-cols-2" : ""
                }`}
            >
                <div className={`order-2 md:order-1 ${contactIntro ? "" : "mx-auto max-w-4xl text-center"}`}>
                    {title ? (
                        <Typography
                            as="h2"
                            className="mb-unit-md"
                            variant="contact-intro-title"
                        >
                            {title}
                        </Typography>
                    ) : null}

                    {paragraphs.length ? (
                        <div className="space-y-unit-md text-base leading-relaxed text-on-surface-variant md:text-lg">
                            {paragraphs.map((paragraph) => (
                                <p key={paragraph}>{paragraph}</p>
                            ))}
                        </div>
                    ) : null}

                    {badges.length ? (
                        <div className={`mt-6 flex flex-col gap-3 sm:flex-row ${contactIntro ? "" : "justify-center"}`}>
                            {badges.map((badge) => (
                                <div
                                    className="flex items-center gap-unit-xs"
                                    key={badge.label}
                                >
                                    <span
                                        className={`h-2 w-2 animate-pulse rounded-full ${badge.className}`}
                                    />
                                    <span
                                        className={`text-xs uppercase tracking-widest md:text-sm ${badge.textClassName}`}
                                    >
                                        {badge.label}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : null}
                </div>

                {contactIntro ? (
                    <div className="order-1 md:order-2">
                        <div className="glass-panel group overflow-hidden rounded-xl p-2">
                            <Image
                                alt={imageAlt || title}
                                className="aspect-[3/2] h-auto w-full object-cover"
                                height={800}
                                sizes="(max-width: 768px) 100vw, 50vw"
                                src={contactIntro}
                                variant="contact-intro"
                                width={1200}
                            />
                        </div>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
