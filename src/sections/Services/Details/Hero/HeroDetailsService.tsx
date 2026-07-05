
import HeroRightComponent from "@/components/Service/Details/Hero/HeroRight/HeroRight";
import HeroLeftComponent from "@/components/Service/Details/Hero/HeroLeftComponent/HeroLeftComponent";

export default function HeroDetailsService() {
    return (
        <div className="grid lg:grid-cols-2 gap-unit-xl items-center">

            {/* LEFT */}
<HeroLeftComponent />
            {/* RIGHT */}
<HeroRightComponent/>
        </div>
    );
}