import FeaturedCardComponent from "../../../components/Service/Featured/Cards/FeaturedCardComponent";
import FeatureCardTypographyComponent from "@/components/Service/Featured/Typography/FeatureCardTypographyComponent";

export default function FeaturedSection() {
    return (

        <section className="py-unit-xl bg-surface-container-lowest">
            <div className="px-margin-desktop max-w-container-max mx-auto">
               <FeatureCardTypographyComponent/>
                <div className="grid md:grid-cols-3 gap-unit-lg">
           <FeaturedCardComponent/>

                </div>
            </div>
        </section>
    )

}