import HeroButtons from "@/components/About/HeroComponents/HeroButtonsComponent";
import HeroTypography from "@/components/About/HeroComponents/HeroTypography";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText } from "@/lib/translations";

const heroKeys = [
    "about.hero.title",
    "about.hero.description",
    "about.hero.cta.primary",
    "about.hero.cta.secondary",
];

export default async function HeroSection() {
    const { locale, translations } = await getSiteSettings();

    if (!hasTranslatedText(translations, heroKeys, locale)) return null;

    return (
        <section className="relative flex min-h-[58svh] items-center justify-center overflow-hidden px-5 pt-20 md:min-h-[64svh] md:pt-24">
            <div className="topology-overlay pointer-events-none absolute inset-0" />
            <div className="absolute inset-0 flex items-center justify-center">
                <div className="aspect-square w-[min(800px,150vw)] animate-pulse-slow rounded-full bg-primary/5 blur-[120px]" />
            </div>
            <div className="relative z-10 mx-auto max-w-container-max px-0 text-center md:px-8 xl:px-14">
                <HeroTypography />
                <HeroButtons />
            </div>
        </section>
    );
}
