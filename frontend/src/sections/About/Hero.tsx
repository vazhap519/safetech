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
        <section className="relative flex min-h-[60vh] items-center justify-center overflow-hidden pt-24 md:min-h-[70vh] md:pt-32">
            <div className="topology-overlay pointer-events-none absolute inset-0" />
            <div className="absolute inset-0 flex items-center justify-center">
                <div className="aspect-square w-[min(800px,150vw)] animate-pulse-slow rounded-full bg-primary/5 blur-[120px]" />
            </div>
            <div className="relative z-10 mx-auto max-w-container-max px-margin-desktop text-center">
                <HeroTypography />
                <HeroButtons />
            </div>
        </section>
    );
}
