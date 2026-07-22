import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import ActionLink from "@/components/ui/ActionLink";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Hero() {
    const { branding, locale, translations } = await getSiteSettings();
    const eyebrow = translateText(translations, "home.hero.eyebrow", locale, null);
    const titlePrefix = translateText(
        translations,
        "home.hero.titlePrefix",
        locale,
        null,
    );
    const titleAccent = translateText(
        translations,
        "home.hero.titleAccent",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "home.hero.description",
        locale,
        null,
    );
    const primaryCta = translateText(
        translations,
        "home.hero.primaryCta",
        locale,
        null,
    );
    const secondaryCta = translateText(
        translations,
        "home.hero.secondaryCta",
        locale,
        null,
    );
    const imageAlt = translateText(
        translations,
        "home.hero.imageAlt",
        locale,
        null,
    );

    if (
        !eyebrow &&
        !titlePrefix &&
        !titleAccent &&
        !description &&
        !primaryCta &&
        !secondaryCta
    ) {
        return null;
    }

    return (
        <section className="relative mx-auto flex min-h-[calc(100svh-2rem)] max-w-container-max items-center overflow-hidden px-5 pb-12 pt-28 sm:px-6 md:pb-14 lg:px-14 lg:pb-12 lg:pt-28">
            <div className="relative z-10 grid w-full grid-cols-1 items-center gap-8 lg:grid-cols-2 lg:gap-12 xl:gap-16">
                <div className="order-1 space-y-5 text-center lg:text-left">
                    {eyebrow ? (
                        <div className="glass-card mx-auto inline-flex items-center gap-2 rounded-full px-4 py-2 lg:mx-0">
                            <span className="status-dot" />
                            <span className="text-xs font-semibold uppercase text-secondary md:text-sm">
                                {eyebrow}
                            </span>
                        </div>
                    ) : null}

                    {titlePrefix || titleAccent ? (
                        <Typography
                            as="h1"
                            className="mx-auto max-w-[800px] lg:mx-0"
                            variant="hero"
                        >
                            {titlePrefix}
                            {titlePrefix && titleAccent ? " " : ""}
                            {titleAccent ? (
                                <span className="text-primary">{titleAccent}</span>
                            ) : null}
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

                    {primaryCta || secondaryCta ? (
                        <div className="flex flex-col items-center justify-center gap-3 pt-1 sm:flex-row lg:items-start lg:justify-start">
                            {primaryCta ? (
                                <ConsultationTrigger className="inline-flex min-h-12 max-w-full items-center justify-center rounded-lg bg-primary-container px-6 py-3 text-center text-base font-semibold text-on-primary-container shadow-lg shadow-blue-500/20 transition-all hover:brightness-110 motion-safe:hover:-translate-y-1">
                                    {primaryCta}
                                </ConsultationTrigger>
                            ) : null}
                            {secondaryCta ? (
                                <ActionLink
                                    className="min-h-12 px-6 py-3 text-base"
                                    href="/services"
                                    variant="glass"
                                >
                                    {secondaryCta}
                                </ActionLink>
                            ) : null}
                        </div>
                    ) : null}
                </div>

                {branding.defaultImage ? (
                    <div className="order-2 relative mx-auto w-full max-w-[650px]">
                        <div className="relative aspect-[16/10] w-full overflow-hidden rounded-2xl">
                            <Image
                                alt={imageAlt || branding.siteName}
                                className="h-full w-full object-contain"
                                height={410}
                                priority
                                sizes="(max-width: 768px) 100vw, (max-width: 1280px) 50vw, 720px"
                                src={branding.defaultImage}
                                variant="home-hero"
                                width={610}
                            />
                        </div>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
