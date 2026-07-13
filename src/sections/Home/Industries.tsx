import Industriescomponent from "@/components/Home/Industriescomponent";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const industries = [
    {
        key: "home.industries.items.0",
        icon: "apartment",
        fallback: { ka: "ოფისები", en: "Offices", ru: "Офисы" },
    },
    {
        key: "home.industries.items.1",
        icon: "storefront",
        fallback: { ka: "რიტეილი", en: "Retail", ru: "Ритейл" },
    },
    {
        key: "home.industries.items.2",
        icon: "warehouse",
        fallback: { ka: "საწყობები", en: "Warehouses", ru: "Склады" },
    },
    {
        key: "home.industries.items.3",
        icon: "manufacturing",
        fallback: { ka: "საწარმოები", en: "Factories", ru: "Производство" },
    },
];

export default async function Industries() {
    const { locale, translations } = await getSiteSettings();

    return (
        <section
            className="
                relative
                mx-auto
                max-w-container-max
                overflow-hidden
                px-5
                py-20
                md:px-10
                md:py-28
                xl:px-margin-desktop
            "
        >
            <div
                className="
                    mb-14
                    text-center
                    md:mb-20
                "
            >
                <span
                    className="
                        mb-4
                        inline-block
                        font-mono-sm
                        text-mono-sm
                        uppercase
                        tracking-[0.3em]
                        text-primary
                    "
                >
                    {translateText(
                        translations,
                        "home.industries.eyebrow",
                        locale,
                        {
                            ka: "ინდუსტრიები",
                            en: "Industries",
                            ru: "Отрасли",
                        },
                    )}
                </span>

                <Typography as="h2" variant="section-title">
                    {translateText(translations, "home.industries.title", locale, {
                        ka: "ინდუსტრიები",
                        en: "Industries",
                        ru: "Отрасли",
                    })}
                </Typography>

                <Typography
                    as="p"
                    variant="section-description"
                    className="mx-auto max-w-2xl"
                >
                    {translateText(
                        translations,
                        "home.industries.description",
                        locale,
                        {
                            ka: "უსაფრთხოების, ქსელური და IT ინფრასტრუქტურის გადაწყვეტილებები სხვადასხვა ინდუსტრიისთვის.",
                            en: "Security, networking, and IT infrastructure solutions for different industries.",
                            ru: "Решения по безопасности, сетевой и IT-инфраструктуре для разных отраслей.",
                        },
                    )}
                </Typography>
            </div>

            <div
                className="
                    grid
                    grid-cols-1
                    gap-6
                    md:gap-gutter
                    sm:grid-cols-2
                    xl:grid-cols-4
                "
            >
                {industries.map((item) => (
                    <Industriescomponent
                        icon={item.icon}
                        key={item.key}
                        title={translateText(
                            translations,
                            item.key,
                            locale,
                            item.fallback,
                        )}
                    />
                ))}
            </div>

            <div
                className="
                    absolute
                    bottom-[-200px]
                    right-[-200px]
                    -z-10
                    h-[500px]
                    w-[500px]
                    rounded-full
                    bg-primary/5
                    blur-[150px]
                "
            />
        </section>
    );
}
