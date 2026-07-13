import FaqComponent from "@/components/Service/Details/FaqComponent/FaqComponent";
import Typography from "@/components/ui/Typography";

export default function Faq(){
    return (
        <section className="py-unit-xl px-margin-desktop max-w-4xl mx-auto scroll-reveal">
            <Typography as={"h2"} variant={"section-title"} className={"font-headline-xl text-headline-xl text-center mb-unit-xl"}>
                ხშირად დასმული კითხვები
            </Typography>
            <div className="space-y-unit-md">
                <FaqComponent/>

            </div>
        </section>

    )
}