import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function StoryomponentsImage(){
    const { branding } = await getSiteSettings();

    return (
        <div className="relative group">
            <div
                className="absolute -inset-4 bg-primary/20 blur-2xl group-hover:bg-primary/30 transition-all duration-500 rounded-3xl"></div>
            <Image
                variant="contact-support"
                src={branding.defaultImage}
                alt="SafeTech-ის გუნდისა და კომპანიის ისტორია"

                sizes="(max-width: 768px) 100vw, 50vw"

                className="
        h-[320px]
        sm:h-[420px]
        lg:h-[500px]
        grayscale-[0.2]
        group-hover:grayscale-0
        transition-all
        duration-700
    "
            />
        </div>
    )
}
