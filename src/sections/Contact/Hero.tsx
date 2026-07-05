// import Button from "@/components/ui/Button";
// import HeroTypography from "@/components/ui/HeroTypography";
//
// export default function HeroSection(){
//     return (
//         <section
//             className="relative h-[60vh] flex items-center justify-center overflow-hidden hero-mesh topology-grid pt-20">
//             <div className="absolute inset-0 pointer-events-none">
//                 <div
//                     className="absolute top-1/4 left-1/4 w-96 h-96 bg-primary/10 rounded-full blur-[120px] animated-glow"></div>
//                 <div
//                     className="absolute bottom-1/4 right-1/4 w-64 h-64 bg-secondary/10 rounded-full blur-[100px] animated-glow"
//                     style={{
//                         animationDelay: "-2s",
//                     }}></div>
//             </div>
//             <div className="container max-w-container-max px-margin-desktop text-center relative z-10">
//                 <HeroTypography
//                     as="h1"
//                     variant="contact-title"
//                 >
//                     დავგეგმოთ თქვენი უსაფრთხო
//                     <br className="hidden md:block" />
//                     {" "}IT ინფრასტრუქტურა
//                 </HeroTypography>
//                 <p className="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto mb-unit-lg">
//                     მიიღეთ პროფესიონალური კონსულტაცია ვიდეოსამეთვალყურეო, ქსელური, დაშვების კონტროლის და სერვერული
//                     ინფრასტრუქტურის მიმართულებით.
//                 </p>
//                 <Button variant="contact-hero"
//                    >
//                     კონსულტაციის მოთხოვნა
//                 </Button>
//             </div>
//         </section>
//     )
// }

import ActionLink from "@/components/ui/ActionLink";
import Typography from "@/components/ui/Typography";

export default function Hero() {
    return (
        <section
            className="
                relative
                min-h-[70vh]
                md:min-h-[75vh]
                flex
                items-center
                justify-center
                overflow-hidden
                hero-mesh
                topology-grid
                pt-24
                md:pt-28
                px-4
            "
        >
            {/* Background Effects */}
            <div className="absolute inset-0 pointer-events-none">

                <div
                    className="
                        absolute
                        top-1/4
                        left-1/4
                        w-48
                        h-48
                        md:w-96
                        md:h-96
                        bg-primary/10
                        rounded-full
                        blur-[80px]
                        md:blur-[120px]
                        animated-glow
                    "
                />

                <div
                    className="
                        absolute
                        bottom-1/4
                        right-1/4
                        w-32
                        h-32
                        md:w-64
                        md:h-64
                        bg-secondary/10
                        rounded-full
                        blur-[60px]
                        md:blur-[100px]
                        animated-glow
                    "
                    style={{
                        animationDelay: "-2s",
                    }}
                />
            </div>

            {/* Content */}
            <div
                className="
                    max-w-container-max
                    mx-auto
                    px-margin-desktop
                    text-center
                    relative
                    z-10
                "
            >

                {/* Title */}
                <Typography
                    as="h1"
                    variant="contact-title"
                    className="
                        max-w-5xl
                        mx-auto
                    "
                >
                    დავგეგმოთ თქვენი უსაფრთხო
                    <br className="hidden md:block" />
                    {" "}
                    IT ინფრასტრუქტურა
                </Typography>

                {/* Description */}
                <Typography
                    as="p"
                    variant="description"
                    className="
                        max-w-2xl
                        mx-auto
                        mb-unit-lg
                    "
                >
                    მიიღეთ პროფესიონალური კონსულტაცია
                    ვიდეოსამეთვალყურეო, ქსელური,
                    დაშვების კონტროლის და სერვერული
                    ინფრასტრუქტურის მიმართულებით.
                </Typography>

                {/* CTA */}
                <div
                    className="
                        flex
                        justify-center
                    "
                >
                    <ActionLink href="#contact-form" className="rounded-full px-unit-lg py-4">
                        კონსულტაციის მოთხოვნა
                    </ActionLink>
                </div>

            </div>
        </section>
    );
}
