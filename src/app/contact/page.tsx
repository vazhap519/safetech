import ContactSchema from "@/components/seo/ContactSchema";
import { createMetadata } from "@/lib/seo";
import { getSiteSettings } from "@/lib/site-settings";
import FaqSection from "@/sections/Contact/FaqSection";
import FinalSection from "@/sections/Contact/Final";
import Form from "@/sections/Contact/Form";
import Hero from "@/sections/Contact/Hero";
import Info from "@/sections/Contact/Info";
import Intro from "@/sections/Contact/intro";
import Support from "@/sections/Contact/Support";

export async function generateMetadata() {
    const { branding } = await getSiteSettings();

    return createMetadata({
        title: "კონტაქტი",
        description:
            "დაგვიკავშირდით CCTV, დაშვების კონტროლის, ქსელური და სერვერული ინფრასტრუქტურის პროექტებისთვის.",
        path: "/contact",
        keywords: [
            "კონტაქტი",
            "SafeTech",
            "CCTV თბილისი",
            "IT ინფრასტრუქტურა",
        ],
        image: branding.defaultImage,
        siteName: branding.siteName,
    });
}

export default function Contact() {
    return (
        <>
            <ContactSchema />
            <Hero />
            <Intro />
            <Form />
            <Info />
            <Support />
            <FaqSection />
            <FinalSection />
        </>
    );
}
