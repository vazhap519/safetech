import HomeSchema from "@/components/seo/HomeSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import Cta from "@/sections/Home/Cta";
import Hero from "@/sections/Home/Hero";
import Industries from "@/sections/Home/Industries";
import Infrastructure from "@/sections/Home/Infrastructure";
import Projects from "@/sections/Home/Projects";
import Services from "@/sections/Home/Services";
import Trust from "@/sections/Home/Trust";
import Why from "@/sections/Home/Why";

export async function generateMetadata() {
    const { branding } = await getSiteSettings();

    return createMetadata({
        title: "მთავარი",
        description:
            "ვიდეოსამეთვალყურეო, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროფესიონალური გადაწყვეტილებები.",
        path: "/",
        keywords: [
            "CCTV",
            "IT ინფრასტრუქტურა",
            "ქსელები",
            "სერვერები",
            "უსაფრთხოების სისტემები",
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
