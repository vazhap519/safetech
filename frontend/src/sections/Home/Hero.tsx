import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import ActionLink from "@/components/ui/ActionLink";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getPageImages } from "@/lib/page-images";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Hero() {
    const [{ branding, contact, locale, translations }, { homeHero }] = await Promise.all([
        getSiteSettings(),
        getPageImages(),
    ]);
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
    const conversionHint = translateText(
        translations,
        "home.hero.conversionHint",
        locale,
        {
            ka: "დატოვეთ სახელი და ნომერი — დაგიკავშირდებით მოთხოვნის დასაზუსტებლად.",
            en: "Leave your name and phone number — we’ll contact you to clarify your request.",
            ru: "Оставьте имя и номер — мы свяжемся с вами, чтобы уточнить задачу.",
        },
    );
    const callCta = translateText(
        translations,
        "home.hero.callCta",
        locale,
        {
            ka: "დარეკვა",
            en: "Call now",
            ru: "Позвонить",
        },
    );
    const imageAlt = translateText(
        translations,
        "home.hero.imageAlt",
        locale,
        null,
    );
    const phoneHref = contact.phone
        ? `tel:${contact.phone.replace(/[^\d+]/g, "")}`
        : "";

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
        <section className="relative mx-auto flex min-h-[clamp(32rem,72svh,44rem)] max-w-container-max items-center overflow-hidden px-5 pb-12 pt-28 sm:px-6 md:pb-14 lg:px-14 lg:pb-12 lg:pt-28">
            <div
                className={`relative z-10 grid w-full grid-cols-1 items-center gap-8 ${
                    homeHero ? "lg:grid-cols-2 lg:gap-12 xl:gap-16" : "lg:grid-cols-1"
                }`}
            >
                <div
                    className={`order-1 space-y-5 text-center ${
                        homeHero ? "lg:text-left" : "mx-auto max-w-4xl"
                    }`}
                >
                    {eyebrow ? (
                        <div className={`glass-card mx-auto inline-flex items-center gap-2 rounded-full px-4 py-2 ${homeHero ? "lg:mx-0" : ""}`}>
                            <span className="status-dot" />
                            <span className="text-xs font-semibold uppercase text-secondary md:text-sm">
                                {eyebrow}
                            </span>
                        </div>
                    ) : null}

                    {titlePrefix || titleAccent ? (
                        <Typography
                            as="h1"
                            className={`mx-auto max-w-[800px] ${homeHero ? "lg:mx-0" : ""}`}
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
                            className={`mx-auto max-w-[620px] ${homeHero ? "lg:mx-0" : ""}`}
                            variant="description"
                        >
                            {description}
                        </Typography>
                    ) : null}

                    {primaryCta || secondaryCta ? (
                        <div
                            className={`flex flex-col items-center justify-center gap-3 pt-1 sm:flex-row ${
                                homeHero ? "lg:items-start lg:justify-start" : ""
                            }`}
                        >
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

                    {conversionHint || phoneHref ? (
                        <div
                            className={`mx-auto flex max-w-[620px] flex-col items-center gap-2 text-sm text-on-surface-variant sm:flex-row sm:flex-wrap sm:justify-center ${
                                homeHero ? "lg:mx-0 lg:justify-start" : ""
                            }`}
                        >
                            {conversionHint ? <span>{conversionHint}</span> : null}
                            {phoneHref ? (
                                <a
                                    className="font-semibold text-primary underline-offset-4 hover:underline"
                                    href={phoneHref}
                                >
                                    {callCta}: {contact.phone}
                                </a>
                            ) : null}
                        </div>
                    ) : null}
                </div>

                {homeHero ? (
                    <div className="order-2 relative mx-auto hidden w-full max-w-[650px] lg:block">
                        <div className="relative aspect-video w-full overflow-hidden rounded-2xl">
                            <Image
                                alt={imageAlt || branding.siteName}
                                className="h-full w-full object-contain"
                                height={900}
                                priority
                                quality={68}
                                sizes="(max-width: 1023px) 1px, (max-width: 1280px) 46vw, 610px"
                                src={homeHero}
                                variant="home-hero"
                                width={1600}
                            />
                        </div>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
