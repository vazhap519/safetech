import Image from "@/components/ui/Image";
import { getPageImages } from "@/lib/page-images";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function HeroImage() {
    const [{ servicesHero }, { branding, locale, translations }] = await Promise.all([
        getPageImages(),
        getSiteSettings(),
    ]);

    if (!servicesHero) return null;

    return (
        <div className="relative overflow-hidden rounded-2xl border border-outline-variant/30 p-2 glass-card md:p-4">
            <Image
                alt={translateText(
                    translations,
                    "services.hero.imageAlt",
                    locale,
                    branding.siteName,
                )}
                className="aspect-[12/7] h-auto w-full rounded-xl object-cover"
                height={700}
                priority
                sizes="(max-width: 768px) 100vw, 50vw"
                src={servicesHero}
                width={1200}
            />
        </div>
    );
}
