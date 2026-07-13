export default function Timeline(){
    return (
        <section className="py-unit-xl px-margin-desktop scroll-reveal">
            <h2 className="font-headline-xl text-headline-xl mb-unit-xl">სამუშაო პროცესი</h2>
            <div className="relative">
                <div className="absolute left-8 top-0 h-full w-px bg-primary/20 hidden md:block"></div>
                <div className="space-y-unit-xl relative">
                    <div className="flex flex-col md:flex-row gap-unit-lg items-start relative">
                        <div
                            className="w-16 h-16 rounded-full bg-primary text-on-primary flex items-center justify-center font-headline-lg text-headline-lg z-10 shrink-0">1
                        </div>
                        <div className="glass-card p-unit-lg rounded-2xl flex-1">
                            <h4 className="font-headline-md text-headline-md text-white mb-unit-xs">კონსულტაცია</h4>
                            <p className="font-body-md text-body-md text-on-surface-variant">თქვენი მოთხოვნების და
                                საჭიროებების დეტალური განხილვა.</p>
                        </div>
                    </div>
                    <div className="flex flex-col md:flex-row gap-unit-lg items-start relative">
                        <div
                            className="w-16 h-16 rounded-full bg-primary/20 border border-primary text-primary flex items-center justify-center font-headline-lg text-headline-lg z-10 shrink-0">2
                        </div>
                        <div className="glass-card p-unit-lg rounded-2xl flex-1">
                            <h4 className="font-headline-md text-headline-md text-white mb-unit-xs">შეფასება</h4>
                            <p className="font-body-md text-body-md text-on-surface-variant">ობიექტის ადგილზე
                                დათვალიერება და რისკების ანალიზი.</p>
                        </div>
                    </div>
                    <div className="flex flex-col md:flex-row gap-unit-lg items-start relative">
                        <div
                            className="w-16 h-16 rounded-full bg-primary/20 border border-primary text-primary flex items-center justify-center font-headline-lg text-headline-lg z-10 shrink-0">3
                        </div>
                        <div className="glass-card p-unit-lg rounded-2xl flex-1">
                            <h4 className="font-headline-md text-headline-md text-white mb-unit-xs">პროექტირება</h4>
                            <p className="font-body-md text-body-md text-on-surface-variant">ინდივიდუალური საპროექტო
                                გეგმის და ხარჯთაღრიცხვის მომზადება.</p>
                        </div>
                    </div>
                    <div className="flex flex-col md:flex-row gap-unit-lg items-start relative">
                        <div
                            className="w-16 h-16 rounded-full bg-primary/20 border border-primary text-primary flex items-center justify-center font-headline-lg text-headline-lg z-10 shrink-0">4
                        </div>
                        <div className="glass-card p-unit-lg rounded-2xl flex-1">
                            <h4 className="font-headline-md text-headline-md text-white mb-unit-xs">მონტაჟი</h4>
                            <p className="font-body-md text-body-md text-on-surface-variant">კაბელირება და კამერების
                                პროფესიონალური ინსტალაცია.</p>
                        </div>
                    </div>
                    <div className="flex flex-col md:flex-row gap-unit-lg items-start relative">
                        <div
                            className="w-16 h-16 rounded-full bg-primary/20 border border-primary text-primary flex items-center justify-center font-headline-lg text-headline-lg z-10 shrink-0">5
                        </div>
                        <div className="glass-card p-unit-lg rounded-2xl flex-1">
                            <h4 className="font-headline-md text-headline-md text-white mb-unit-xs">კონფიგურაცია</h4>
                            <p className="font-body-md text-body-md text-on-surface-variant">სისტემის გამართვა,
                                პროგრამული უზრუნველყოფა და ტრენინგი.</p>
                        </div>
                    </div>
                    <div className="flex flex-col md:flex-row gap-unit-lg items-start relative">
                        <div
                            className="w-16 h-16 rounded-full bg-primary/20 border border-primary text-primary flex items-center justify-center font-headline-lg text-headline-lg z-10 shrink-0">6
                        </div>
                        <div className="glass-card p-unit-lg rounded-2xl flex-1">
                            <h4 className="font-headline-md text-headline-md text-white mb-unit-xs">მხარდაჭერა</h4>
                            <p className="font-body-md text-body-md text-on-surface-variant">უწყვეტი ტექნიკური
                                მომსახურება და მონიტორინგი.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    )
}