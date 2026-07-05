import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import { getSiteSettings } from "@/lib/site-settings";

import type { ServiceDetail } from "../model/types";

function toPhoneHref(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export default async function ServiceCta({
    service,
}: {
    service: ServiceDetail;
}) {
    const { contact } = await getSiteSettings();

    return (
        <section
            className="relative overflow-hidden px-margin-desktop py-unit-xl"
            aria-labelledby="service-cta-title"
        >
            <div aria-hidden="true" className="ambient-glow bottom-0 right-0" />
            <div className="glass-card space-y-unit-lg rounded-3xl p-unit-lg text-center sm:rounded-[40px] sm:p-unit-xl">
                <h2
                    className="font-display-lg text-[34px] leading-tight text-white sm:text-[46px] md:text-[58px]"
                    id="service-cta-title"
                >
                    გჭირდებათ {service.name}?
                </h2>
                <p className="mx-auto max-w-2xl text-lg leading-relaxed text-on-surface-variant">
                    დაგვიკავშირდით და მიიღეთ უფასო პირველადი კონსულტაცია და
                    თქვენს ობიექტზე მორგებული შეთავაზება.
                </p>
                <div className="flex flex-wrap justify-center gap-unit-md">
                    <ConsultationTrigger className="glow-button rounded-xl bg-primary-container px-unit-xl py-unit-md font-headline-md text-on-primary-container shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-1 hover:shadow-[0_0_30px_rgba(37,99,235,0.4)]">
                        უფასო კონსულტაცია
                    </ConsultationTrigger>
                    <a
                        className="rounded-xl border border-outline-variant bg-white/5 px-unit-xl py-unit-md font-headline-md text-white transition-colors hover:bg-white/10"
                        href={toPhoneHref(contact.phone)}
                    >
                        დაგვირეკეთ
                    </a>
                </div>
            </div>
        </section>
    );
}
