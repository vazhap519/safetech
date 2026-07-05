import Typography from "@/components/ui/Typography";
import WhoComponent from "@/components/About/WhoComponent";

export default function WhoSection() {
    return (
        <section className="py-unit-xl">
            <div className="max-w-container-max mx-auto px-margin-desktop">
                <div className="text-center mb-unit-xl">
                    <Typography as="h2" variant="section-title">
                        მიმართულებები
                    </Typography>
                    <Typography as="p" variant="section-description">
                        პროფესიონალური გადაწყვეტილებები ნებისმიერი მასშტაბისთვის
                    </Typography>
                </div>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-gutter">
                    <WhoComponent/>
                </div>
            </div>
        </section>

    )
}