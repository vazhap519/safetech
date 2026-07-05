import Typography from "@/components/ui/Typography";

export default function WhyComponent() {
    return (
        <div className="p-unit-lg glass-card rounded-2xl border-l-4 border-l-primary">
            <span className="material-symbols-outlined text-primary text-4xl mb-4">verified</span>
            <Typography as="h3" variant={"why-component-header"}>
                ავტორიზებული პარტნიორი
            </Typography>
            {/*<h3 className="font-headline-md text-headline-md mb-2"></h3>*/}
            <Typography
                as="p"
                variant="section-description"
                className="max-w-2xl mx-auto"
            >ჩვენ ვართ Cisco-ს, Hikvision-ის და Ubiquiti-ს სერტიფიცირებული პარტნიორები საქართველოში.</Typography>
        </div>
    )
}