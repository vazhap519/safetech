export default function Cta(){
    return (
        <section className="py-unit-xl px-margin-desktop relative overflow-hidden">
            <div className="ambient-glow bottom-0 right-0"></div>
            <div className="glass-card p-unit-xl rounded-[40px] text-center space-y-unit-lg">
                <h2 className="font-display-lg text-display-lg text-white max-md:font-display-lg-mobile max-md:text-display-lg-mobile">გჭირდებათ
                    პროფესიონალური IP ვიდეოსამეთვალყურეო სისტემები?</h2>
                <p className="font-body-lg text-body-lg text-on-surface-variant max-w-2xl mx-auto">დაგვიკავშირდით
                    დღესვე და მიიღეთ უფასო კონსულტაცია თქვენი ობიექტისთვის.</p>
                <div className="flex flex-wrap justify-center gap-unit-md">
                    <button
                        className="bg-primary text-on-primary-fixed px-unit-xl py-unit-md rounded-xl font-headline-md text-headline-md glow-button">უფასო
                        კონსულტაცია
                    </button>
                    <button
                        className="border border-outline-variant bg-white/5 text-white px-unit-xl py-unit-md rounded-xl font-headline-md text-headline-md hover:bg-white/10 transition-all">დაგვიკავშირდით
                    </button>
                </div>
            </div>
        </section>

    )
}