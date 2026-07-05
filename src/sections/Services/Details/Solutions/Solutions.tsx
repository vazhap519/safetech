import SolutionHeaderH2 from "../../../../components/Service/Details/Solutions/SolutionsHeaders/SolutionHeaderH2";
import SolutionHeaderH4 from "../../../../components/Service/Details/Solutions/SolutionsHeaders/SolutionHeaderH4";
import Image from "@/components/ui/Image";
import { getSiteSettings } from "@/lib/site-settings";

export default async function Solutions(){
    const { branding } = await getSiteSettings();
    return (
        <section className="py-unit-xl px-margin-desktop scroll-reveal">
            <SolutionHeaderH2/>
            <div className="grid grid-cols-1 md:grid-cols-12 grid-rows-2 gap-unit-md h-auto md:h-[600px]">
                <div
                    className="md:col-span-4 glass-card p-unit-lg rounded-3xl flex flex-col justify-end bg-[url('https://images.unsplash.com/photo-1557597774-9d273605dfa9?auto=format&amp;fit=crop&amp;q=80&amp;w=600')] bg-cover bg-center relative overflow-hidden group">
                    <div className="absolute inset-0 bg-gradient-to-t from-background to-transparent"></div>
                    <div className="relative z-10">

                        <SolutionHeaderH4/>
                        <p className="font-label-md text-label-md text-on-surface-variant">თანამშრომლების
                            მონიტორინგი და შიდა უსაფრთხოება.</p>
                    </div>
                </div>
                <div
                    className="md:col-span-8 glass-card p-unit-lg rounded-3xl flex flex-col justify-end relative overflow-hidden group">
                    <div
                        className="absolute inset-0 bg-gradient-to-r from-background via-background/40 to-transparent z-10"></div>
                    <Image alt="საწყობის ვიდეომონიტორინგის სისტემა"
                         className="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                         fill
                         sizes="(max-width: 768px) 100vw, 66vw"
                         src={branding.defaultImage}/>
                    <div className="relative z-20 max-w-md">
                        <h4 className="font-headline-md text-headline-md text-white">საწყობის CCTV</h4>
                        <p className="font-label-md text-label-md text-on-surface-variant">ინვენტარის დაცვა და
                            ლოგისტიკური პროცესების კონტროლი AI-ს დახმარებით.</p>
                    </div>
                </div>
                <div className="md:col-span-3 glass-card p-unit-md rounded-3xl flex flex-col gap-unit-sm">
                    <span className="material-symbols-outlined text-secondary" data-icon="videocam">videocam</span>
                    <h4 className="font-headline-md text-headline-md text-white">გარე კამერები</h4>
                    <p className="font-label-md text-label-md text-on-surface-variant">IP67 დაცვა, ღამის ხედვა და
                        ვანდალგამძლეობა.</p>
                </div>
                <div className="md:col-span-3 glass-card p-unit-md rounded-3xl flex flex-col gap-unit-sm">
                        <span className="material-symbols-outlined text-secondary"
                              data-icon="analytics">analytics</span>
                    <h4 className="font-headline-md text-headline-md text-white">ვიდეო ანალიტიკა</h4>
                    <p className="font-label-md text-label-md text-on-surface-variant">ხალხის დათვლა, სახის ამოცნობა
                        და სითბური რუკები.</p>
                </div>
                <div className="md:col-span-3 glass-card p-unit-md rounded-3xl flex flex-col gap-unit-sm">
                        <span className="material-symbols-outlined text-secondary"
                              data-icon="directions_car">directions_car</span>
                    <h4 className="font-headline-md text-headline-md text-white">ANPR სისტემა</h4>
                    <p className="font-label-md text-label-md text-on-surface-variant">სახელმწიფო ნომრების
                        ავტომატური ამოცნობა და დაშვება.</p>
                </div>
                <div className="md:col-span-3 glass-card p-unit-md rounded-3xl flex flex-col gap-unit-sm">
                        <span className="material-symbols-outlined text-secondary"
                              data-icon="settings_overscan">settings_overscan</span>
                    <h4 className="font-headline-md text-headline-md text-white">PTZ კამერები</h4>
                    <p className="font-label-md text-label-md text-on-surface-variant">მართვადი კამერები ოპტიკური
                        ზუმით დიდი ტერიტორიებისთვის.</p>
                </div>
            </div>
        </section>

    )
}
