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
                overflow-hidden
                py-20
                md:py-28
                px-5
                md:px-10
                xl:px-margin-desktop
                max-w-container-max
                mx-auto
            "
        >

            {/* Section Header */}
            <div
                className="
                    text-center
                    mb-14
                    md:mb-20
                "
            >

                {/* Badge */}
                <span
                        className="
                            inline-block
                        text-primary
                        font-mono-sm
                        text-mono-sm
                        uppercase
                        tracking-[0.3em]
                        mb-4
                        "
                    >
                        {translateText(translations, "home.industries.eyebrow", locale, {
                            ka: "ინდუსტრიები",
                            en: "Industries",
                            ru: "Отрасли",
                        })}
                    </span>

                <Typography
                    as="h2"
                    variant="section-title"
                >
                    ინდუსტრიები
                </Typography>

                {/* Description */}
                <Typography
                    as="p"
                    variant="section-description"
                    className="
        max-w-2xl
        mx-auto
    "
                >
                    უსაფრთხოების, ქსელური და
                    IT ინფრასტრუქტურის გადაწყვეტილებები
                    სხვადასხვა ინდუსტრიისთვის.
                </Typography>
            </div>

            {/* Industries Grid */}
            <div
                className="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    xl:grid-cols-4
                    gap-6
                    md:gap-gutter
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

            {/* Background Glow */}
            <div
                className="
                    absolute
                    bottom-[-200px]
                    right-[-200px]
                    w-[500px]
                    h-[500px]
                    bg-primary/5
                    rounded-full
                    blur-[150px]
                    -z-10
                "
            ></div>
        </section>
    );
}
