import FeaturedCardComponent from "@/components/Service/Featured/Cards/FeaturedCardComponent";
import FeatureCardTypographyComponent from "@/components/Service/Featured/Typography/FeatureCardTypographyComponent";
import { getBackendServices } from "@/lib/backend";

export default async function FeaturedSection({
    selectedService,
}: {
    selectedService?: string;
}) {
    const services = await getBackendServices();

    if (!services.length) return null;

    const selected = selectedService
        ? services.find((service) => service.slug === selectedService)
        : undefined;
    const selectedCategory = selected?.category?.slug;
    const related = selectedCategory
        ? services.filter(
              (service) =>
                  service.slug !== selectedService &&
                  service.category?.slug === selectedCategory,
          )
        : [];
    const fallback = services.filter((service) => service.slug !== selectedService);
    const visibleServices = [...related, ...fallback.filter((service) => !related.includes(service))].slice(
        0,
        3,
    );

    if (!visibleServices.length) return null;

    return (
        <section className="bg-surface-container-lowest py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                <FeatureCardTypographyComponent />
                <div className="grid gap-unit-lg md:grid-cols-3">
                    {visibleServices.map((service) => (
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
