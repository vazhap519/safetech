import AboutSchema from "@/components/seo/AboutSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
import CtaSection from "@/sections/About/CtaSection";
import HeroSection from "@/sections/About/Hero";
import HowSection from "@/sections/About/How";
import NumbersSections from "@/sections/About/Numbers";
import StorySection from "@/sections/About/Story";
import TeamSection from "@/sections/About/Team";
import WhatSection from "@/sections/About/What";
import WhoSection from "@/sections/About/Who";
import WhySection from "@/sections/About/Why";

export async function generateMetadata() {
    const { branding, locale, translations } = await getSiteSettings();

    return createMetadata({
        title: translateText(translations, "meta.about.title", locale, null),
        description: translateText(
            translations,
            "meta.about.description",
            locale,
            null,
        ),
        path: "/about",
        locale,
        keywords: [
            "SafeTech",
            "about safetech",
            "IT company Georgia",
            "security systems integrator",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

export default function About() {
    return (
        <>
            <AboutSchema />
            <HeroSection />
            <StorySection />
            <WhoSection />
            <WhatSection />
            <HowSection />
            <NumbersSections />
            <TeamSection />
            <WhySection />
            <CtaSection />
        </>
    );
}
