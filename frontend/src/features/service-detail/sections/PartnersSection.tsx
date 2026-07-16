import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

const partners = ["Hikvision", "Dahua", "Uniview", "MikroTik", "Ubiquiti", "TP-Link", "Ruijie"];

export default async function PartnersSection() {
    const { locale, translations } = await getSiteSettings();

    return (
        <section aria-label={translateText(translations, "service.detail.partners.ariaLabel", locale, {
            ka: "ტექნოლოგიური პარტნიორები",
            en: "Technology partners",
            ru: "Технологические партнеры",
        })} className="border-y border-white/5 bg-background px-margin-desktop py-unit-lg">
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
