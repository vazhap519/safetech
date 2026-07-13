import Image from "@/components/ui/Image"
import { getSiteSettings } from "@/lib/site-settings";

export default async function HeroImage() {
    const { branding } = await getSiteSettings();

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
                alt="კიბერუსაფრთხოებისა და IT ინფრასტრუქტურის სერვისები"
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
