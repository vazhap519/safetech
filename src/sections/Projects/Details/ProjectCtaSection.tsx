import LocalizedLink from "@/components/ui/LocalizedLink";

export default function ProjectCtaSection() {
    return (
        <section className="relative px-margin-desktop py-unit-xl">
            <div className="glass-card relative mx-auto max-w-container-max overflow-hidden rounded-3xl border-primary-container p-unit-lg text-center sm:p-unit-xl">
                <div
                    aria-hidden="true"
                    className="ambient-glow -top-1/2 left-1/2 h-[600px] w-[min(600px,140vw)] -translate-x-1/2 bg-primary-container"
                />
                <h2 className="relative mb-8 font-display-lg text-display-lg-mobile leading-tight md:text-display-lg">
                    დავგეგმოთ თქვენი შემდეგი ინფრასტრუქტურული პროექტი
                </h2>
                <p className="relative mx-auto mb-12 max-w-2xl text-body-lg leading-relaxed text-on-surface-variant">
                    გამოიყენეთ ჩვენი გამოცდილება თქვენი ბიზნესის უსაფრთხოებისა
                    და ტექნოლოგიური მდგრადობისთვის.
                </p>
                <div className="relative flex flex-col justify-center gap-unit-md sm:flex-row">
                    <LocalizedLink
                        className="rounded-2xl bg-primary-container px-8 py-5 text-center font-label-md text-on-primary-container transition-transform motion-safe:hover:scale-105"
                        href="/contact"
                    >
                        პროექტის შეფასება
                    </LocalizedLink>
                    <LocalizedLink
                        className="rounded-2xl border border-outline-variant bg-surface-variant/20 px-8 py-5 text-center font-label-md transition-colors hover:bg-surface-variant/40"
                        href="/contact"
                    >
                        დაგვიკავშირდით
                    </LocalizedLink>
                </div>
            </div>
        </section>
    );
}
