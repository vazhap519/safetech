import AboutSchema from "@/components/seo/AboutSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
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
    const { branding } = await getSiteSettings();

    return createMetadata({
        title: "ჩვენ შესახებ",
        description:
            "გაიცანით SafeTech, პროფესიონალური IT და უსაფრთხოების ინფრასტრუქტურის გუნდი.",
        path: "/about",
        keywords: [
            "SafeTech",
            "ჩვენ შესახებ",
            "IT კომპანია",
            "უსაფრთხოების სისტემები",
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
