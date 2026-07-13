import Servicecomponent from "../../components/Home/Servicecomponent";
import { getBackendServices } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Services() {
    const [services, { locale, translations }] = await Promise.all([
        getBackendServices(),
        getSiteSettings(),
    ]);

    if (!services.length) return null;

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
                    flex
                    flex-col
                    items-center
                    text-center
                    md:mb-20
                "
            >
                <span
                    className="
                        mb-4
                        font-mono-sm
                        text-mono-sm
                        uppercase
                        tracking-[0.3em]
                        text-primary
                    "
                >
                    {translateText(translations, "home.services.eyebrow", locale, {
                        ka: "ძირითადი შესაძლებლობები",
                        en: "Our Capabilities",
                        ru: "Наши возможности",
                    })}
                </span>

                <h2
                    className="
                        max-w-4xl
                        font-headline-xl
                        text-[36px]
                        leading-tight
                        text-on-surface
                        sm:text-[42px]
                        md:text-[52px]
                        xl:text-headline-xl
                    "
                >
                    {translateText(translations, "home.services.title", locale, {
                        ka: "ძირითადი სერვისები",
                        en: "Core Services",
                        ru: "Основные услуги",
                    })}
                </h2>

                <p
                    className="
                        mt-5
                        max-w-2xl
                        text-base
                        leading-relaxed
                        text-on-surface-variant
                        md:text-lg
                    "
                >
                    {translateText(
                        translations,
                        "home.services.description",
                        locale,
                        {
                            ka: "თანამედროვე უსაფრთხოების, ქსელური და IT ინფრასტრუქტურის პროფესიონალური გადაწყვეტილებები ბიზნესისთვის.",
                            en: "Professional security, networking, and IT infrastructure solutions for business.",
                            ru: "Профессиональные решения для безопасности, сетевой и IT-инфраструктуры бизнеса.",
                        },
                    )}
                </p>
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
                {services.slice(0, 4).map((service) => (
                    <Servicecomponent key={service.slug} service={service} />
                ))}
            </div>

            <div
                className="
                    absolute
                    right-[-200px]
                    top-[-200px]
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
