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
        <section className="relative mx-auto flex min-h-[90vh] max-w-container-max items-center overflow-hidden px-4 pb-16 pt-24 sm:px-6 md:pb-20 md:pt-32 lg:min-h-screen lg:px-margin-desktop">
            <div className="pointer-events-none absolute right-[-200px] top-0 -z-10 h-[700px] w-[700px] rounded-full bg-primary/5 blur-[150px]" />
            <div className="relative z-10 grid w-full grid-cols-1 items-center gap-12 lg:grid-cols-2 xl:gap-unit-xl">
                <div className="order-1 space-y-6 text-center lg:text-left">
                    {eyebrow ? (
                        <div className="glass-card mx-auto inline-flex items-center gap-2 rounded-full px-4 py-2 lg:mx-0">
                            <span className="status-dot" />
                            <span className="text-xs uppercase tracking-widest text-secondary md:text-sm">
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
                        <div className="flex flex-col items-center justify-center gap-4 pt-2 sm:flex-row lg:items-start lg:justify-start">
                            {primaryCta ? (
                                <ConsultationTrigger className="inline-flex max-w-full items-center justify-center rounded-xl bg-primary-container px-8 py-4 text-center text-lg font-medium text-on-primary-container shadow-lg shadow-blue-500/20 transition-all hover:brightness-110 motion-safe:hover:-translate-y-1">
                                    {primaryCta}
                                </ConsultationTrigger>
                            ) : null}
                            {secondaryCta ? (
                                <ActionLink
                                    className="px-8 py-4 text-lg"
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
                    <div className="order-2 relative mx-auto w-full max-w-[720px]">
                        <div className="relative aspect-[16/10] w-full overflow-hidden rounded-[28px]">
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
