import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function FeaturedCardComponent() {
    const { branding, locale, translations } = await getSiteSettings();

    return (
        <div className="relative overflow-hidden rounded-2xl aspect-[4/5] group">
            <Image
                alt={translateText(
                    translations,
                    "services.featured.card.imageAlt",
                    locale,
                    {
                        ka: "ვიდეოსამეთვალყურეო სისტემის გამორჩეული გადაწყვეტა",
                        en: "Featured video surveillance solution",
                        ru: "Рекомендуемое решение для видеонаблюдения",
                    },
                )}
                className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                sizes="(max-width: 768px) 100vw, 50vw"
                src={branding.defaultImage}
            />
            <div className="absolute inset-0 bg-gradient-to-t from-background to-transparent opacity-90" />
            <div className="absolute bottom-0 p-unit-lg">
                <Typography
                    as="h3"
                    className="font-headline-md text-headline-md mb-2 text-white"
                    variant="section-title"
                >
                    {translateText(translations, "services.featured.card.title", locale, {
                        ka: "CCTV გადაწყვეტილებები",
                        en: "CCTV solutions",
                        ru: "CCTV-решения",
                    })}
                </Typography>
                <Typography
                    as="p"
                    className="font-body-md text-body-md text-white/70"
                    variant="description"
                >
                    {translateText(
                        translations,
                        "services.featured.card.description",
                        locale,
                        {
                            ka: "24/7 მონიტორინგი და უსაფრთხოება.",
                            en: "24/7 monitoring and security.",
                            ru: "Круглосуточный мониторинг и безопасность.",
                        },
                    )}
                </Typography>
            </div>
        </div>
    );
}
