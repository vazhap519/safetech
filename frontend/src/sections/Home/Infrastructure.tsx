import Infrastructurecomponent from "@/components/Home/Infrastructurecomponent";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const infrastructureItems = [
    { icon: "schema", key: "home.infrastructure.items.0" },
    { icon: "lan", key: "home.infrastructure.items.1" },
    { icon: "monitoring", key: "home.infrastructure.items.2" },
];

export default async function Infrastructure() {
    const { branding, locale, translations } = await getSiteSettings();
    const eyebrow = translateText(
        translations,
        "home.infrastructure.eyebrow",
        locale,
        null,
    );
    const title = translateText(
        translations,
        "home.infrastructure.title",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "home.infrastructure.description",
        locale,
        null,
    );
    const imageAlt = translateText(
        translations,
        "home.infrastructure.imageAlt",
        locale,
        null,
    );
    const items = infrastructureItems
        .map((item) => ({
            ...item,
            description: translateText(
                translations,
                `${item.key}.description`,
                locale,
                null,
            ),
            title: translateText(translations, `${item.key}.title`, locale, null),
        }))
        .filter((item) => item.title || item.description);

    if (!eyebrow && !title && !description && !items.length) return null;

    return (
        <section className="relative overflow-hidden bg-surface-container-lowest/30 py-20 md:py-28">
            <div className="mx-auto grid max-w-container-max grid-cols-1 items-center gap-16 px-5 md:px-10 lg:grid-cols-2 xl:gap-unit-xl xl:px-margin-desktop">
                {branding.defaultImage ? (
                    <div className="group relative order-2 mx-auto w-full max-w-[760px] lg:order-1">
                        <div className="absolute -inset-4 rounded-full bg-primary/10 blur-3xl transition-all duration-700 group-hover:bg-primary/20" />
                        <Image
                            alt={imageAlt || branding.siteName}
                            height={410}
                            sizes="(max-width: 768px) 100vw, 50vw"
                            src={branding.defaultImage}
                            variant="home-infrastructure"
                            width={600}
                        />
                    </div>
                ) : null}

                <div className="order-1 space-y-8 text-center lg:order-2 lg:text-left">
                    {eyebrow ? (
                        <div className="glass-card mx-auto inline-flex items-center gap-2 rounded-full px-4 py-2 lg:mx-0">
                            <span className="status-dot" />
                            <span className="font-mono-sm text-mono-sm uppercase tracking-widest text-secondary">
                                {eyebrow}
                            </span>
                        </div>
                    ) : null}

                    {title ? (
                        <Typography as="h2" variant="section-title">
                            {title}
                        </Typography>
                    ) : null}

                    {description ? (
                        <Typography
                            as="p"
                            className="mx-auto max-w-[620px] lg:mx-0"
                            variant="description"
                        >
                            {description}
                        </Typography>
                    ) : null}

                    {items.length ? (
                        <div className="space-y-5 pt-2">
                            {items.map((item) => (
                                <Infrastructurecomponent
                                    description={item.description}
                                    icon={item.icon}
                                    key={item.key}
                                    title={item.title}
                                />
                            ))}
                        </div>
                    ) : null}
                </div>
            </div>
        </section>
    );
}
