import AboutSchema from "@/components/seo/AboutSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
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
    return createCmsPageMetadata(PAGE_SEO_PRESETS.about);
}

export default function About() {
    return (
        <>
            <CmsPageSchema pageKey="about" fallback={<AboutSchema />} />
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
