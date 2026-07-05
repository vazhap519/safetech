import ShowcaseHeaderComponent from "@/components/Service/Details/ShowcaseComponent/ShowcaseHeaderComponent";

export default function Showcase(){
    return (
    <section className="py-unit-xl bg-surface-container px-margin-desktop scroll-reveal">
        <div className="grid md:grid-cols-2 gap-unit-lg">
            <ShowcaseHeaderComponent/>

        </div>
    </section>
    )
}