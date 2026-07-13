import ServiceOverviewImage
    from "@/components/Service/Details/ServiceOverview/ServiceOverviewImage/ServiceOverviewImage";
import ServiceOverviewHeader
    from "@/components/Service/Details/ServiceOverview/ServiceOverviewHeader/ServiceOverviewHeader";
import ServiceOverviewParagraph
    from "@/components/Service/Details/ServiceOverview/ServiceOverviewParagraph/ServiceOverviewParagraph";
import ServiceOverviewList from "@/components/Service/Details/ServiceOverview/ServiceOverviewList/ServiceOverviewList";

export default function ServiceOverview() {
    return (
        <section
            className="py-unit-xl px-margin-desktop grid md:grid-cols-2 gap-unit-xl items-center scroll-reveal">
            <div className="relative rounded-2xl overflow-hidden glass-card aspect-video group">
               <ServiceOverviewImage/>
                <div
                    className="absolute inset-0 bg-gradient-to-t from-background via-transparent to-transparent opacity-60"></div>
            </div>
            <div className="space-y-unit-lg">
                <ServiceOverviewHeader/>
                <ServiceOverviewParagraph/>
                <ServiceOverviewList/>
            </div>
        </section>
    )
}