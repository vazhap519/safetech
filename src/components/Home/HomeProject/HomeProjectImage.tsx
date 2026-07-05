import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function HomeProjectImageComponent() {
    const { branding } = await getSiteSettings();

    return (
        <Image
            alt="სასტუმროს უსაფრთხოებისა და ქსელური ინფრასტრუქტურის პროექტი"
            sizes="(max-width: 768px) 100vw, 50vw"
            src={branding.defaultImage}
            variant="home-project"
        />
    );
}
