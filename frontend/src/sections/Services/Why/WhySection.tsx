import WhyTypographyComponent from "../../../components/Service/Why/Typography/WhyTypographyComponent";
import WhyGridComponent from "@/components/Service/Why/Grid/WhyGridComponent";

export default function WhySection() {
    return (
        <section className="py-unit-xl px-margin-desktop max-w-container-max mx-auto">
            <WhyTypographyComponent/>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
<WhyGridComponent/>
            </div>
        </section>
    )
}
