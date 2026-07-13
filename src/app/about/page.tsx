import AboutSchema from "@/components/seo/AboutSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";
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
    const t = createTranslator(translations, locale);

    return createMetadata({
        title: t("meta.about.title", {
            ka: "ჩვენ შესახებ | SafeTech გუნდი და გამოცდილება",
            en: "About SafeTech | Team and Experience",
            ru: "О SafeTech | Команда и опыт",
        }),
        description: t("meta.about.description", {
            ka: "გაიცანით SafeTech-ის გუნდი, გამოცდილება და მიდგომა IT ინფრასტრუქტურისა და უსაფრთხოების პროექტებში.",
            en: "Meet the SafeTech team, expertise, and approach to IT infrastructure and security projects.",
            ru: "Познакомьтесь с командой SafeTech, опытом и подходом к проектам IT-инфраструктуры и безопасности.",
        }),
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
