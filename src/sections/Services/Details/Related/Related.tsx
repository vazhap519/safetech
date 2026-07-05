export default function Related(){
    return (
        <section className="py-unit-xl px-margin-desktop bg-surface-container-low scroll-reveal">
            <h2 className="font-headline-xl text-headline-xl mb-unit-xl">მსგავსი სერვისები</h2>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-unit-md">
                <a className="glass-card p-unit-lg rounded-2xl flex flex-col gap-unit-sm hover:border-primary group"
                   href="#">
                        <span
                            className="material-symbols-outlined text-primary group-hover:scale-110 transition-transform"
                            data-icon="wifi">wifi</span>
                    <h5 className="font-headline-md text-headline-md text-white">Wi-Fi სისტემები</h5>
                    <p className="font-label-md text-label-md text-on-surface-variant">მაღალსიჩქარიანი უსადენო
                        ქსელი.</p>
                </a>
                <a className="glass-card p-unit-lg rounded-2xl flex flex-col gap-unit-sm hover:border-primary group"
                   href="#">
                        <span
                            className="material-symbols-outlined text-primary group-hover:scale-110 transition-transform"
                            data-icon="lan">lan</span>
                    <h5 className="font-headline-md text-headline-md text-white">ქსელური ინფრასტრუქტურა</h5>
                    <p className="font-label-md text-label-md text-on-surface-variant">კაბელირება და მონტაჟი.</p>
                </a>
                <a className="glass-card p-unit-lg rounded-2xl flex flex-col gap-unit-sm hover:border-primary group"
                   href="#">
                        <span
                            className="material-symbols-outlined text-primary group-hover:scale-110 transition-transform"
                            data-icon="storage">storage</span>
                    <h5 className="font-headline-md text-headline-md text-white">სერვერები</h5>
                    <p className="font-label-md text-label-md text-on-surface-variant">მონაცემთა შენახვა და
                        მართვა.</p>
                </a>
                <a className="glass-card p-unit-lg rounded-2xl flex flex-col gap-unit-sm hover:border-primary group"
                   href="#">
                        <span
                            className="material-symbols-outlined text-primary group-hover:scale-110 transition-transform"
                            data-icon="settings">settings</span>
                    <h5 className="font-headline-md text-headline-md text-white">IT მხარდაჭერა</h5>
                    <p className="font-label-md text-label-md text-on-surface-variant">სრული აუთსორსინგ
                        მომსახურება.</p>
                </a>
            </div>
        </section>

    )
}