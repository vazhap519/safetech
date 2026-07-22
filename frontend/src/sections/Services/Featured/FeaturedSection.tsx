import FeaturedCardComponent from "@/components/Service/Featured/Cards/FeaturedCardComponent";
import FeatureCardTypographyComponent from "@/components/Service/Featured/Typography/FeatureCardTypographyComponent";
import { getBackendServices } from "@/lib/backend";

export default async function FeaturedSection() {
    const services = await getBackendServices();

    if (!services.length) return null;

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <FeatureCardTypographyComponent />
                <div className="grid gap-unit-lg md:grid-cols-3">
                    {services.slice(0, 3).map((service) => (
                        <FeaturedCardComponent
                            key={service.slug}
                            service={service}
                        />
                    ))}
                </div>
            </div>
        </section>
    );
}
