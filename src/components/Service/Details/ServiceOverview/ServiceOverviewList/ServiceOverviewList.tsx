import { serviceOverview } from "@/lib/serviceOverview";

export default function ServiceOverviewList() {
    return (
        <div className="flex flex-wrap gap-unit-lg">
            {serviceOverview.map((item) => (
                <div
                    key={item.label}
                    className="flex flex-col"
                >
                    <span className="text-secondary font-headline-lg text-headline-lg">
                        {item.value}
                    </span>

                    <span className="font-label-md text-label-md text-on-surface-variant">
                        {item.label}
                    </span>
                </div>
            ))}
        </div>
    );
}