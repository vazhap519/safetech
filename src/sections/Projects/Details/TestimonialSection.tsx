import type { ProjectDetail } from "@/lib/projectDetails";

export default function TestimonialSection({ testimonial }: { testimonial: ProjectDetail["testimonial"] }) {
    return (
        <section className="bg-surface-container-low px-margin-desktop py-unit-xl">
            <figure className="relative mx-auto max-w-4xl text-center">
                <span aria-hidden="true" className="absolute -top-16 left-0 select-none text-9xl text-primary/20">“</span>
                <blockquote className="mb-12 font-headline-lg text-headline-lg italic leading-relaxed">“{testimonial.quote}”</blockquote>
                <figcaption><p className="font-headline-md text-headline-md">{testimonial.author}</p><p className="font-mono-sm text-mono-sm uppercase text-on-surface-variant">{testimonial.role}</p></figcaption>
            </figure>
        </section>
    );
}
