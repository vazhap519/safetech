import Image from "@/components/ui/Image";
import Icon from "@/components/ui/Icon";
import LocalizedLink from "@/components/ui/LocalizedLink";
import Typography from "@/components/ui/Typography";
import { CARD_ACTION_CLASS } from "@/components/ui/cardAction";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

type FeaturedService = {
    description: string;
    slug: string;
    title: string;
};

export default async function FeaturedCardComponent({
    service,
}: {
    service: FeaturedService;
}) {
    const { branding, locale, translations } = await getSiteSettings();
    const detailsLabel = translateText(
        translations,
        "common.readMore",
        locale,
        { ka: "დეტალურად", en: "View details", ru: "Подробнее" },
    );
    const imageAlt = translateText(
        translations,
        "services.featured.card.imageAlt",
        locale,
        service.title,
    );

    if (!service.slug || (!service.title && !service.description)) return null;

    return (
        <article className="group relative aspect-[4/5] overflow-hidden rounded-lg border border-outline-variant/20">
            {branding.defaultImage ? (
                <Image
                    alt={imageAlt || service.title}
                    className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                    sizes="(max-width: 768px) 100vw, 50vw"
                    src={branding.defaultImage}
                />
            ) : null}
            <div className="absolute inset-0 bg-gradient-to-t from-background via-background/70 to-transparent" />
            <div className="absolute inset-x-0 bottom-0 flex flex-col p-unit-lg">
                {service.title ? (
                    <Typography
                        as="h3"
                        className="mb-2 font-headline-md text-headline-md text-white"
                        variant="why-component-header"
                    >
                        {service.title}
                    </Typography>
                ) : null}
                {service.description ? (
                    <Typography
                        as="p"
                        className="font-body-md text-body-md text-white/80"
                        variant="description"
                    >
                        {service.description}
                    </Typography>
                ) : null}
                {detailsLabel ? (
                    <LocalizedLink
                        className={`${CARD_ACTION_CLASS} mt-4`}
                        href={`/services/${service.slug}`}
                        prefetch={false}
                    >
                        {detailsLabel}
                        <Icon
                            aria-hidden="true"
                            className="text-[18px] transition-transform group-hover:translate-x-1"
                            name="arrow_forward"
                        />
                    </LocalizedLink>
                ) : null}
            </div>
        </article>
    );
}
