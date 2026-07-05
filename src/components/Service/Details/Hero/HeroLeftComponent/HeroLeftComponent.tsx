import HeroDotsComponent from "@/components/Service/Details/HeroDotsComponent";
import Typography from "@/components/ui/Typography";
import HeroComponent from "@/components/Service/Details/Hero/HeroComponent";
import HeroButtonsComponent from "@/components/Service/Details/Hero/HeroButtonsComponent";

export default function HeroLeftComponent() {
    return (
        <div className="space-y-unit-lg">
            <HeroDotsComponent />

            <Typography
                as="h1"
                variant="section-description"
                className="
                        font-display-lg
                        text-display-lg
                        leading-tight
                        tracking-tight
                        text-white
                        max-md:font-display-lg-mobile
                        max-md:text-display-lg-mobile
                    "
            >
                პროფესიონალური IP ვიდეოსამეთვალყურეო
                სისტემები ბიზნესისთვის
            </Typography>

            <Typography
                as="p"
                variant="description"
                className="
                        font-body-lg
                        text-body-lg
                        text-on-surface-variant
                        max-w-2xl
                    "
            >
                Enterprise დონის უსაფრთხოების გადაწყვეტილებები
                AI ანალიტიკით და დისტანციური მონიტორინგით.
                უზრუნველყავით თქვენი ბიზნესის სრული კონტროლი.
            </Typography>

            <div className="grid grid-cols-2 md:grid-cols-4 gap-unit-md py-unit-md">
                <HeroComponent />
            </div>

            <HeroButtonsComponent />
        </div>

    )
}
