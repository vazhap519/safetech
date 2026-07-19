import ContactSchema from "@/components/seo/ContactSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import FaqSection from "@/sections/Contact/FaqSection";
import FinalSection from "@/sections/Contact/Final";
import Form from "@/sections/Contact/Form";
import Hero from "@/sections/Contact/Hero";
import Info from "@/sections/Contact/Info";
import Intro from "@/sections/Contact/intro";
import Support from "@/sections/Contact/Support";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.contact);
}

export default function Contact() {
    return (
        <>
            <CmsPageSchema pageKey="contact" fallback={<ContactSchema />} />
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
