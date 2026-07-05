import ActionLink from "@/components/ui/ActionLink";
import { getSiteSettings } from "@/lib/site-settings";

function toPhoneHref(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export default async function CtaSection() {
    const { contact } = await getSiteSettings();

    return (
        <section className="mx-auto mb-unit-xl max-w-container-max px-margin-desktop py-unit-xl">
            <div className="relative overflow-hidden rounded-3xl border-primary/20 bg-primary-container/10 p-unit-lg text-center glass-card sm:p-unit-xl">
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary/10 via-transparent to-transparent" />
                <div className="relative z-10">
                    <h2 className="mb-unit-md font-headline-xl text-headline-xl">
                        გჭირდებათ პროფესიონალური IT ინფრასტრუქტურა?
                    </h2>
                    <p className="mx-auto mb-unit-lg max-w-2xl font-body-lg text-body-lg text-on-surface-variant">
                        დაგვიტოვეთ საკონტაქტო ინფორმაცია და ჩვენი ექსპერტი
                        დაგიკავშირდებათ უფასო კონსულტაციისთვის.
                    </p>
                    <div className="flex flex-col justify-center gap-unit-md sm:flex-row">
                        <ActionLink
                            href="/contact"
                            className="px-unit-xl py-unit-md text-lg"
                        >
                            მოითხოვეთ შეთავაზება
                        </ActionLink>
                        <ActionLink
                            href={toPhoneHref(contact.phone)}
                            variant="glass"
                            className="px-unit-xl py-unit-md text-lg"
                        >
                            დაგვირეკეთ: {contact.phone}
                        </ActionLink>
                    </div>
                </div>
            </div>
        </section>
    );
}
