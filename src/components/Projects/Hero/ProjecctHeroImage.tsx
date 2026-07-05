import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ProjectsHeroImage() {
    const { branding } = await getSiteSettings();

    return (
        <div className="relative w-full h-full">
            <Image
                alt="SafeTech-ის ინფრასტრუქტურული პროექტები"
                className="object-cover opacity-60"
                fill
                sizes="100vw"
                src={branding.defaultImage}
                variant="home-hero"
            />
        </div>
    );
}
