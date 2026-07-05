import Servicecomponent from "../../components/Home/Servicecomponent";
import { getBackendServices } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Services() {
    const [services, { locale, translations }] = await Promise.all([
        getBackendServices(),
        getSiteSettings(),
    ]);
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
                    flex
                    flex-col
                    items-center
                    text-center
                    mb-14
                    md:mb-20
                "
            >

                <span
                    className="
                        text-primary
                        font-mono-sm
                        text-mono-sm
                        tracking-[0.3em]
                        uppercase
                        mb-4
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
                        font-headline-xl
                        text-[36px]
                        sm:text-[42px]
                        md:text-[52px]
                        xl:text-headline-xl
                        text-on-surface
                        leading-tight
                        max-w-4xl
                    "
                >
                    ძირითადი სერვისები
                </h2>

                <p
                    className="
                        mt-5
                        text-on-surface-variant
                        text-base
                        md:text-lg
                        max-w-2xl
                        leading-relaxed
                    "
                >
                    თანამედროვე უსაფრთხოების, ქსელური და
                    IT ინფრასტრუქტურის პროფესიონალური
                    გადაწყვეტილებები ბიზნესისთვის.
                </p>
            </div>

            {/* Services Grid */}
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

                {/* Service Cards */}
                {services.slice(0, 4).map((service) => <Servicecomponent key={service.slug} service={service} />)}
            </div>

            {/* Background Glow */}
            <div
                className="
                    absolute
                    top-[-200px]
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
