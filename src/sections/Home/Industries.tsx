import Infrastructurecomponent from "@/components/Home/Infrastructurecomponent";
import Typography from "@/components/ui/Typography";

export default function Industries() {
    return (
        <section
            className="
                relative
                overflow-hidden
                py-20
                md:py-28
                px-5
                md:px-10
                xl:px-margin-desktop
                max-w-container-max
                mx-auto
            "
        >

            {/* Section Header */}
            <div
                className="
                    text-center
                    mb-14
                    md:mb-20
                "
            >

                {/* Badge */}
                <span
                    className="
                        inline-block
                        text-primary
                        font-mono-sm
                        text-mono-sm
                        uppercase
                        tracking-[0.3em]
                        mb-4
                    "
                >
                    Industries
                </span>

                <Typography
                    as="h2"
                    variant="section-title"
                >
                    ინდუსტრიები
                </Typography>

                {/* Description */}
                <Typography
                    as="p"
                    variant="section-description"
                    className="
        max-w-2xl
        mx-auto
    "
                >
                    უსაფრთხოების, ქსელური და
                    IT ინფრასტრუქტურის გადაწყვეტილებები
                    სხვადასხვა ინდუსტრიისთვის.
                </Typography>
            </div>

            {/* Industries Grid */}
            <div
                className="
                    grid
                    grid-cols-1
                    sm:grid-cols-2
                    xl:grid-cols-4
                    gap-6
                    md:gap-gutter
                "
            >

                <Infrastructurecomponent />
                <Infrastructurecomponent />
                <Infrastructurecomponent />
                <Infrastructurecomponent />
            </div>

            {/* Background Glow */}
            <div
                className="
                    absolute
                    bottom-[-200px]
                    right-[-200px]
                    w-[500px]
                    h-[500px]
                    bg-primary/5
                    rounded-full
                    blur-[150px]
                    -z-10
                "
            ></div>
        </section>
    );
}