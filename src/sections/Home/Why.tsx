import WhyComponent from "@/components/Home/Whycomponent";
import Typography from "@/components/ui/Typography";

export default function Why() {
    return (
        <section
            className="
                relative
                overflow-hidden
                py-20
                md:py-28
                bg-surface-container-low
            "
        >

            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-5
                    md:px-10
                    xl:px-margin-desktop
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
                        Why Choose Us
                    </span>

                    {/* Title */}
                    <Typography
                        as="h2"
                        variant="section-title"
                    >
                        რატომ SafeTech?
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
                        ჩვენი გამოცდილება და ტექნოლოგიური
                        პარტნიორობა გლობალურ ლიდერებთან
                        საშუალებას გვაძლევს შემოგთავაზოთ
                        საუკეთესო ხარისხი.
                    </Typography>
                </div>

                {/* Cards Grid */}
                <div
                    className="
                        grid
                        grid-cols-1
                        sm:grid-cols-2
                        xl:grid-cols-3
                        gap-6
                        md:gap-gutter
                    "
                >

                    <WhyComponent />
                    <WhyComponent />
                    <WhyComponent />
                    <WhyComponent />
                    <WhyComponent />
                    <WhyComponent />
                </div>
            </div>

            {/* Background Glow */}
            <div
                className="
                    absolute
                    top-[-250px]
                    left-[-250px]
                    w-[600px]
                    h-[600px]
                    bg-primary/5
                    rounded-full
                    blur-[160px]
                    -z-10
                "
            ></div>
        </section>
    );
}