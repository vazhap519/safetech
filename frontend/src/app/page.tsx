import CorePageFallback from "@/components/seo/CorePageFallback";
import HomeSchema from "@/components/seo/HomeSchema";
import CmsPageSchema from "@/components/seo/CmsPageSchema";
import { createCmsPageMetadata } from "@/lib/cms-metadata";
import { PAGE_SEO_PRESETS } from "@/lib/page-seo-presets";
import DeferredCta from "@/sections/Home/DeferredCta";
import Hero from "@/sections/Home/Hero";
import Industries from "@/sections/Home/Industries";
import Infrastructure from "@/sections/Home/Infrastructure";
import Projects from "@/sections/Home/Projects";
import Services from "@/sections/Home/Services";
import Testimonials from "@/sections/Home/Testimonials";
import Trust from "@/sections/Home/Trust";
import Why from "@/sections/Home/Why";

export async function generateMetadata() {
    return createCmsPageMetadata(PAGE_SEO_PRESETS.home);
}

export default function HomePage() {
    return (
        <>
            <CmsPageSchema pageKey="home" fallback={<HomeSchema />} />
            <CorePageFallback pageKey="home" />
            <div className="overflow-x-hidden">
                <Hero />
                <Trust />
                <Services />
                <Infrastructure />
                <Projects />
                <Testimonials />
                <Why />
                <Industries />
                <DeferredCta />
            </div>
        </>
    );
}
