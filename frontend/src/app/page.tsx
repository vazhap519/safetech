import HomeSchema from "@/components/seo/HomeSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";
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

    return createMetadata({
        title: translateText(translations, "meta.home.title", locale, null),
        description: translateText(
            translations,
            "meta.home.description",
            locale,
            null,
        ),
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
