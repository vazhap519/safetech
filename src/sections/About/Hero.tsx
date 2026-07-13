
import HeroTypography from "@/components/About/HeroComponents/HeroTypography";
import HeroButtons from "@/components/About/HeroComponents/HeroButtonsComponent";
export default function HeroSection(){
    return (
        <section className="
    relative
    min-h-[60vh]
    md:min-h-[70vh]
    pt-24
    md:pt-32
    flex
    items-center
    justify-center
    overflow-hidden
    ">
            <div className="topology-overlay absolute inset-0 pointer-events-none"></div>
            <div className="absolute inset-0 flex items-center justify-center">
                <div className="w-[min(800px,150vw)] aspect-square bg-primary/5 rounded-full blur-[120px] animate-pulse-slow"></div>
            </div>
            <div className="relative z-10 max-w-container-max mx-auto px-margin-desktop text-center">
<HeroTypography/>
<HeroButtons/>
            </div>
        </section>
    )
}
