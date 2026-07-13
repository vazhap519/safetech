import HomeProjectImageComponent from "@/components/Home/HomeProject/HomeProjectImage";
import HomeProjectHeaderComponent from "@/components/Home/HomeProject/HomeProjectHeaderComponent";
import HomeProjectDescriptionComponent from "@/components/Home/HomeProject/HomeProjectDescriptionComponent";

export default function Projectcomponent(){
    return (
        <div className="glass-card rounded-3xl overflow-hidden group">
            <div className="h-64 relative overflow-hidden">
              <HomeProjectImageComponent/>
                <div className="absolute inset-0 bg-gradient-to-t from-[#070B14] to-transparent opacity-60"></div>
                <div className="absolute bottom-4 left-6">
                    <span
                        className="bg-primary/20 text-primary px-3 py-1 rounded-full font-mono-sm text-mono-sm uppercase">Hospitality</span>
                </div>
            </div>
            <div className="p-unit-lg space-y-4">
<HomeProjectHeaderComponent/>

    <div className="grid grid-cols-2 gap-4">
<HomeProjectDescriptionComponent/>

                </div>
            </div>
        </div>
    )
}