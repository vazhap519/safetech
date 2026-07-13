import HomeSchema from "@/components/seo/HomeSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";
import Cta from "@/sections/Home/Cta";
import Hero from "@/sections/Home/Hero";
import Industries from "@/sections/Home/Industries";
import Infrastructure from "@/sections/Home/Infrastructure";
import Projects from "@/sections/Home/Projects";
import Services from "@/sections/Home/Services";
import Trust from "@/sections/Home/Trust";
import Why from "@/sections/Home/Why";

export async function generateMetadata() {
    const { branding, locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return createMetadata({
        title: t("meta.home.title", {
            ka: "IT ინფრასტრუქტურა და უსაფრთხოების სისტემები ბიზნესისთვის",
            en: "IT Infrastructure and Security Systems for Business",
            ru: "IT-инфраструктура и системы безопасности для бизнеса",
        }),
        description: t("meta.home.description", {
            ka: "SafeTech უზრუნველყოფს ვიდეომეთვალყურეობას, დაშვების კონტროლს, ქსელურ და სერვერულ ინფრასტრუქტურას საქართველოში.",
            en: "SafeTech delivers CCTV, access control, networking, and server infrastructure solutions for businesses in Georgia.",
            ru: "SafeTech внедряет видеонаблюдение, контроль доступа, сетевую и серверную инфраструктуру для бизнеса в Грузии.",
        }),
        path: "/",
        locale,
        keywords: [
            "CCTV",
            "IT infrastructure",
            "networking",
            "server systems",
            "security systems",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

export default function HomePage() {
    return (
        <>
            <HomeSchema />
            <div className="overflow-x-hidden">
                <Hero />
                <Trust />
                <Services />
                <Infrastructure />
                <Projects />
                <Why />
                <Industries />
                <Cta />
            </div>
        </>
    );
}
