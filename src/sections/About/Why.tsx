
import WhyComponent from "@/components/About/WhyComponent";
import WhyTypographyComponent from "@/components/Service/Why/Typography/WhyTypographyComponent";

export default function WhySection(){
    return (
        <section className="py-unit-xl">
            <div className="max-w-container-max mx-auto px-margin-desktop">
             <WhyTypographyComponent/>

                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-gutter">
<WhyComponent/>
<WhyComponent/>
<WhyComponent/>
<WhyComponent/>
                </div>
            </div>
        </section>
    )
}
