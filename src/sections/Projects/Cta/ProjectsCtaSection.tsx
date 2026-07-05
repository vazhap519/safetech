import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";

export default function ProjectsCtaSection() {
    return (
        <section className="relative overflow-hidden px-margin-desktop py-unit-xl" aria-labelledby="projects-cta-title">
            <div className="glass-card relative z-10 mx-auto max-w-4xl rounded-2xl px-unit-lg py-unit-xl text-center sm:px-unit-xl">
                <h2 className="mb-6 font-display-lg text-display-lg-mobile text-white md:text-headline-xl" id="projects-cta-title">დავგეგმოთ თქვენი ინფრასტრუქტურა</h2>
                <p className="mx-auto mb-unit-lg max-w-2xl font-body-lg text-body-lg leading-relaxed text-on-surface-variant">
                    მიიღეთ პროფესიონალური კონსულტაცია და თქვენს საჭიროებებზე მორგებული საპროექტო გადაწყვეტა.
                </p>
                <ConsultationTrigger className="inline-flex max-w-full rounded-full bg-primary-container px-unit-lg py-4 text-center font-label-md font-bold text-on-primary-container transition-transform motion-safe:hover:scale-105">
                    კონსულტაციის მოთხოვნა
                </ConsultationTrigger>
            </div>
            <div aria-hidden="true" className="ambient-glow absolute left-1/2 top-1/2 z-0 h-[600px] w-[min(600px,140vw)] -translate-x-1/2 -translate-y-1/2" />
        </section>
    );
}
