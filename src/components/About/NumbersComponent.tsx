export default function NumbersComponent() {
    return(
        <div className="glass-card p-unit-md sm:p-unit-lg rounded-2xl text-center relative overflow-hidden group">
        <div className="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div className="text-headline-xl font-display-lg text-primary mb-2">500+</div>
        <div className="text-label-md text-on-surface-variant tracking-widest uppercase">კამერა</div>
        <div className="mt-4 flex justify-center"><div className="w-12 h-1 bg-primary/20 rounded-full overflow-hidden">
            <div className="h-full bg-primary w-3/4"></div></div></div>
        </div>
    )
}
