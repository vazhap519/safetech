import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ShowCaseImageComponent() {
    const { branding } = await getSiteSettings();

    return (
        <Image
            alt="IT Project Installation"
            sizes="(max-width: 768px) 100vw, 50vw"
            src={branding.defaultImage}
            variant="Service-showCase"
        />
    );
}
