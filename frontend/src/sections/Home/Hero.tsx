import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import ActionLink from "@/components/ui/ActionLink";
import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Hero() {
    const { branding, locale, translations } = await getSiteSettings();

    return (
        <section
            className="
                relative
                mx-auto
                flex
                min-h-[90vh]
                max-w-container-max
                items-center
                overflow-hidden
                px-4
                pb-16
                pt-24
                sm:px-6
                md:pb-20
                md:pt-32
                lg:min-h-screen
                lg:px-margin-desktop
            "
        >
            <div
                aria-hidden="true"
                className="
                    pointer-events-none
                    absolute
                    right-[-200px]
                    top-0
                    -z-10
                    h-[700px]
                    w-[700px]
                    rounded-full
                    bg-primary/5
                    blur-[150px]
                "
            />

            <div
                className="
                    relative
                    z-10
                    grid
                    w-full
                    grid-cols-1
                    items-center
                    gap-12
                    lg:grid-cols-2
                    xl:gap-unit-xl
                "
            >
                <div
                    className="
                        order-1
                        space-y-6
                        text-center
                        lg:order-1
                        lg:text-left
                    "
                >
                    <div
                        className="
                            glass-card
                            mx-auto
                            inline-flex
                            items-center
                            gap-2
                            rounded-full
                            px-4
                            py-2
                            lg:mx-0
                        "
                    >
                        <span className="status-dot" />

                        <span
                            className="
                                text-xs
                                uppercase
                                tracking-widest
                                text-secondary
                                md:text-sm
                            "
                        >
                            {translateText(
                                translations,
                                "home.hero.eyebrow",
                                locale,
                                {
                                    ka: "ბიზნეს IT გადაწყვეტილებები",
                                    en: "Enterprise IT Solutions",
                                    ru: "Корпоративные IT-решения",
                                },
                            )}
                        </span>
                    </div>

                    <Typography
                        as="h1"
                        variant="hero"
                        className="
                            mx-auto
                            max-w-[800px]
                            lg:mx-0
                        "
                    >
                        {translateText(
                            translations,
                            "home.hero.titlePrefix",
                            locale,
                            {
                                ka: "თანამედროვე ბიზნესის უსაფრთხო",
                                en: "Secure",
                                ru: "Надежная",
                            },
                        )}{" "}
                        <span className="text-primary">
                            {translateText(
                                translations,
                                "home.hero.titleAccent",
                                locale,
                                {
                                    ka: "IT ინფრასტრუქტურა",
                                    en: "IT infrastructure for modern business",
                                    ru: "IT-инфраструктура для современного бизнеса",
                                },
                            )}
                        </span>
                    </Typography>

                    <Typography
                        as="p"
                        variant="description"
                        className="
                            mx-auto
                            max-w-[620px]
                            lg:mx-0
                        "
                    >
                        {translateText(
                            translations,
                            "home.hero.description",
                            locale,
                            {
                                ka: "ვიდეოსამეთვალყურეო, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალური გადაწყვეტები თანამედროვე ბიზნესისთვის.",
                                en: "Professional CCTV, access control, networking, and server infrastructure solutions for modern businesses.",
                                ru: "Профессиональные решения для видеонаблюдения, контроля доступа, сетевой и серверной инфраструктуры для современного бизнеса.",
                            },
                        )}
                    </Typography>

                    <div
                        className="
                            flex
                            flex-col
                            items-center
                            justify-center
                            gap-4
                            pt-2
                            sm:flex-row
                            lg:items-start
                            lg:justify-start
                        "
                    >
                        <ConsultationTrigger className="inline-flex max-w-full items-center justify-center rounded-xl bg-primary-container px-8 py-4 text-center text-lg font-medium text-on-primary-container shadow-lg shadow-blue-500/20 transition-all hover:brightness-110 motion-safe:hover:-translate-y-1">
                            {translateText(
                                translations,
                                "home.hero.primaryCta",
                                locale,
                                {
                                    ka: "კონსულტაციის მოთხოვნა",
                                    en: "Request Consultation",
                                    ru: "Запросить консультацию",
                                },
                            )}
                        </ConsultationTrigger>

                        <ActionLink
                            href="/services"
                            variant="glass"
                            className="px-8 py-4 text-lg"
                        >
                            {translateText(
                                translations,
                                "home.hero.secondaryCta",
                                locale,
                                {
                                    ka: "სერვისების ნახვა",
                                    en: "View Services",
                                    ru: "Смотреть услуги",
                                },
                            )}
                        </ActionLink>
                    </div>
                </div>

                <div
                    className="
                        order-2
                        relative
                        mx-auto
                        w-full
                        max-w-[720px]
                        lg:order-2
                    "
                >
                    <div
                        aria-hidden="true"
                        className="
                            pointer-events-none
                            absolute
                            inset-0
                            rounded-full
                            bg-primary/20
                            blur-[100px]
                        "
                    />

                    <div
                        className="
                            relative
                            aspect-[16/10]
                            w-full
                            overflow-hidden
                            rounded-[28px]
                        "
                    >
                        <Image
                            variant="home-hero"
                            src={branding.defaultImage}
                            alt={translateText(
                                translations,
                                "home.hero.imageAlt",
                                locale,
                                {
                                    ka: "SafeTech-ის უსაფრთხოებისა და IT ინფრასტრუქტურის გადაწყვეტა",
                                    en: "SafeTech security and IT infrastructure solution",
                                    ru: "Решение SafeTech для безопасности и IT-инфраструктуры",
                                },
                            )}
                            priority
                            sizes="(max-width: 768px) 100vw, (max-width: 1280px) 50vw, 720px"
                            width={610}
                            height={410}
                            className="h-full w-full object-contain"
                        />
                    </div>
                </div>
            </div>
        </section>
    );
}
