import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function HeroRightImage() {
    const { branding } = await getSiteSettings();

    return (
        <Image
            alt="Topology Diagram"
            className="
                w-full
                max-w-[700px]
                rounded-3xl
                shadow-2xl
            "
            height={410}
            sizes="(max-width: 768px) 100vw, 50vw"
            src={branding.defaultImage}
            variant="home-hero"
            width={600}
        />
    );
}
