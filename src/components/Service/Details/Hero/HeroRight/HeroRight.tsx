import HeroRightImage from "@/components/Service/Details/Hero/HeroRight/HeroRightImage";

export default function HeroRightComponent() {
    return (
        <div className="relative flex justify-center">

            <div
                className="
                        absolute
                        inset-0
                        bg-primary/20
                        blur-[120px]
                        rounded-full
                    "
            />

            <div className="relative animate-float">
<HeroRightImage/>
            </div>
        </div>
    )

}