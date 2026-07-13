import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function ServiceOverviewImage() {
    const { branding } = await getSiteSettings();

    return (
        <Image
            alt="სერვისის ტექნიკური ინფრასტრუქტურა"
            className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            src={branding.defaultImage}
            variant="Service-showCase"
        />
    );
}
