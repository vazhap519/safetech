import ServiceCardComponent from "@/components/Service/ServiceCard/ServiceCardComponent";
import ServiceTypographyComponent from "@/components/Service/ServiceTypography/ServiceTypographyComponent";
import { getBackendServices } from "@/lib/backend";

export default async function ServiceSection() {
    const services = await getBackendServices();

    return (
        <section className="mx-auto max-w-container-max px-margin-desktop py-unit-xl">
            <ServiceTypographyComponent />

            <div className="mb-unit-xl flex flex-col gap-2 text-sm text-on-surface-variant sm:flex-row sm:items-center sm:justify-between">
                <p>{services.length} აქტიური სერვისი</p>
                <p>ყველა სერვისი ხელმისაწვდომია უშუალოდ ამ გვერდიდან.</p>
            </div>

            <div className="mb-unit-xl grid gap-gutter md:grid-cols-2 lg:grid-cols-3">
                {services.map((service) => (
                    <ServiceCardComponent key={service.slug} service={service} />
                ))}
            </div>
        </section>
    );
}
