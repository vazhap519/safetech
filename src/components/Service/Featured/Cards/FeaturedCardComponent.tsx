import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";

export default async function FeaturedCardComponent() {
    const { branding } = await getSiteSettings();

    return (
        <div className="relative overflow-hidden rounded-2xl aspect-[4/5] group">
            <Image
                alt="ვიდეოსამეთვალყურეო სისტემის გამორჩეული გადაწყვეტა"
                className="absolute inset-0 h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                sizes="(max-width: 768px) 100vw, 50vw"
                src={branding.defaultImage}
            />
            <div className="absolute inset-0 bg-gradient-to-t from-background to-transparent opacity-90" />
            <div className="absolute bottom-0 p-unit-lg">
                <Typography
                    as="h3"
                    className="font-headline-md text-headline-md mb-2 text-white"
                    variant="section-title"
                >
                    CCTV გადაწყვეტილებები
                </Typography>
                <Typography
                    as="p"
                    className="font-body-md text-body-md text-white/70"
                    variant="description"
                >
                    24/7 მონიტორინგი და უსაფრთხოება.
                </Typography>
            </div>
        </div>
    );
}
