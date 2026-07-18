import Enterprise from "@/components/Service/Hero/Enterprise";
import Header from "@/components/Service/Hero/Header";
import HeroImage from "@/components/Service/Hero/HeroImage";
import HeroSupport from "@/components/Service/Hero/HeroSupport";
import HeroTypography from "@/components/Service/Hero/HeroTypography";
import HeroVerified from "@/components/Service/Hero/HeroVerified";
import Overly from "@/components/Service/Hero/Overly";
import { getSiteSettings } from "@/lib/site-settings";
import { hasTranslatedText } from "@/lib/translations";

const heroKeys = [
    "services.hero.eyebrow",
    "services.hero.titlePrefix",
    "services.hero.titleAccent",
    "services.hero.titleSuffix",
    "services.hero.description",
    "services.hero.iso",
    "services.hero.support",
];

export default async function HeroSection() {
    const { locale, translations } = await getSiteSettings();

    if (!hasTranslatedText(translations, heroKeys, locale)) {
        return null;
    }

    return (
        <header className="relative isolate mx-auto max-w-container overflow-hidden px-5 pb-16 pt-32 md:px-10 md:pb-24 md:pt-44 xl:px-16">
            <Overly />
            <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-2 lg:gap-20">
                <div className="order-2 z-10 max-w-2xl lg:order-1">
                    <Enterprise />
                    <Header />
                    <HeroTypography />
                    <div className="mb-unit-xl flex flex-wrap gap-unit-md">
                        <HeroVerified />
                        <HeroSupport />
                    </div>
                </div>
                <div className="group relative">
                    <div className="absolute inset-0 rounded-full bg-primary/20 blur-[100px] transition-all duration-700 group-hover:bg-primary/30" />
                    <HeroImage />
                </div>
            </div>
        </header>
    );
}
