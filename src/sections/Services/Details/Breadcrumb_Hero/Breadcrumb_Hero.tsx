import BreadcrumbComponent from "@/sections/Services/Details/Breadcrumb/Breadcrumb";
import HeroDetailsService from "@/sections/Services/Details/Hero/HeroDetailsService";
export default function Breadcrumb_Hero(){
    return (
        <section className="relative min-h-[80vh] flex flex-col justify-center px-margin-desktop overflow-hidden">
            <div className="ambient-glow -top-20 -left-20"></div>
            <BreadcrumbComponent/>
            <HeroDetailsService/>
        </section>
    )
}