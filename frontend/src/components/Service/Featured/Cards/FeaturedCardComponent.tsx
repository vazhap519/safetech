import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FeaturedCardComponent() {
    const { branding, locale, translations } = await getSiteSettings();
    const title = translateText(
        translations,
        "services.featured.card.title",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "services.featured.card.description",
        locale,
        null,
    );
    const imageAlt = translateText(
        translations,
        "services.featured.card.imageAlt",
        locale,
        null,
    );

    if (!title && !description) {
        return null;
    }

    return (
        <div className="group relative aspect-[4/5] overflow-hidden rounded-2xl">
            <Image
                alt={imageAlt || title}
                className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                sizes="(max-width: 768px) 100vw, 50vw"
                src={branding.defaultImage}
            />
            <div className="absolute inset-0 bg-gradient-to-t from-background to-transparent opacity-90" />
            <div className="absolute bottom-0 p-unit-lg">
                {title ? (
                    <Typography
                        as="h3"
                        className="mb-2 font-headline-md text-headline-md text-white"
                        variant="section-title"
                    >
                        {title}
                    </Typography>
                ) : null}
                {description ? (
                    <Typography
                        as="p"
                        className="font-body-md text-body-md text-white/70"
                        variant="description"
                    >
                        {description}
                    </Typography>
                ) : null}
            </div>
        </div>
    );
}
