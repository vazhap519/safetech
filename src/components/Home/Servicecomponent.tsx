import Link from "next/link";

type ServiceCard = { slug: string; title: string; description: string; icon: string };

export default function Servicecomponent({ service }: { service: ServiceCard }){
    return (
        <div className="glass-card p-unit-lg rounded-3xl flex flex-col gap-unit-md">
            <div className="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary">
                <span className="material-symbols-outlined !text-[40px]">{service.icon}</span>
            </div>
            <h3 className="font-headline-md text-headline-md text-white">{service.title}</h3>
            <p className="text-on-surface-variant font-body-md">{service.description}</p>
            <Link className="mt-auto inline-flex items-center gap-2 font-label-md text-primary hover:text-secondary" href={`/services/${service.slug}`}>დეტალურად <span aria-hidden="true">→</span></Link>
        </div>

    )
}
