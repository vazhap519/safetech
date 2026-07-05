import Typography from "@/components/ui/Typography";
import Benefit from "@/components/Service/Details/BenefitComponent/Benefit";

export default function KeyBenefits() {
    return (
        <section className="py-unit-xl px-margin-desktop scroll-reveal">
            <Typography as={"h2"} variant={'section-title'} className={"font-headline-xl text-headline-xl text-center mb-unit-xl"}>რატომ SafeTech?</Typography>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                {/*// <!-- Benefit 1 -->*/}
                <Benefit/>

            </div>
        </section>

    )
}