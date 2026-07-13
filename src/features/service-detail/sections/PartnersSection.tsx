const partners = ["Hikvision", "Dahua", "Uniview", "MikroTik", "Ubiquiti", "TP-Link", "Ruijie"];

export default function PartnersSection() {
    return (
        <section aria-label="ტექნოლოგიური პარტნიორები" className="border-y border-white/5 bg-background px-margin-desktop py-unit-lg">
            <ul className="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                {partners.map((partner) => (
                    <li
                        className="rounded-full border border-white/8 bg-white/[0.03] px-4 py-2 font-headline-md text-base text-on-surface-variant transition-all duration-300 hover:-translate-y-0.5 hover:border-secondary/30 hover:bg-secondary/10 hover:text-on-surface"
                        key={partner}
                    >
                        {partner}
                    </li>
                ))}
            </ul>
        </section>
    );
}
