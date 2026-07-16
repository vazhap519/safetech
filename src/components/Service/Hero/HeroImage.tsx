import Image from "@/components/ui/Image"
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroImage() {
    const { branding, locale, translations } = await getSiteSettings();

    return (
        <div
            className="
    relative

    rounded-2xl
    overflow-hidden

    border border-outline-variant/30

    glass-card

    p-2
    md:p-4
"
        >
            <Image
                src={branding.defaultImage}
                alt={translateText(translations, "services.hero.imageAlt", locale, {
                    ka: "კიბერუსაფრთხოებისა და IT ინფრასტრუქტურის სერვისები",
                    en: "Cybersecurity and IT infrastructure services",
                    ru: "Услуги кибербезопасности и IT-инфраструктуры",
                })}
                sizes="(max-width: 768px) 100vw, 50vw"

width={610}
                height={410}

                priority
                className="
        w-full
        h-[260px]
        sm:h-[340px]
        md:h-[450px]
        lg:h-[560px]
        object-cover
        rounded-xl
    "
            />

        </div>
    )
}
