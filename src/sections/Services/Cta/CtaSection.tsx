import ActionLink from "@/components/ui/ActionLink";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

function toPhoneHref(phone: string) {
    return `tel:${phone.replace(/[^\d+]/g, "")}`;
}

export default async function CtaSection() {
    const { contact, locale, translations } = await getSiteSettings();

    return (
        <section className="mx-auto mb-unit-xl max-w-container-max px-margin-desktop py-unit-xl">
            <div className="glass-card relative overflow-hidden rounded-3xl border-primary/20 bg-primary-container/10 p-unit-lg text-center sm:p-unit-xl">
                <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary/10 via-transparent to-transparent" />
                <div className="relative z-10">
                    <h2 className="mb-unit-md font-headline-xl text-headline-xl">
                        {translateText(translations, "services.cta.title", locale, {
                            ka: "გჭირდებათ პროფესიონალური IT ინფრასტრუქტურა?",
                            en: "Need professional IT infrastructure?",
                            ru: "Нужна профессиональная IT-инфраструктура?",
                        })}
                    </h2>
                    <p className="mx-auto mb-unit-lg max-w-2xl font-body-lg text-body-lg text-on-surface-variant">
                        {translateText(
                            translations,
                            "services.cta.description",
                            locale,
                            {
                                ka: "დაგვიტოვეთ საკონტაქტო ინფორმაცია და ჩვენი ექსპერტი დაგიკავშირდებათ უფასო კონსულტაციისთვის.",
                                en: "Leave your contact details and our specialist will contact you for a free consultation.",
                                ru: "Оставьте контактные данные, и наш специалист свяжется с вами для бесплатной консультации.",
                            },
                        )}
                    </p>
                    <div className="flex flex-col justify-center gap-unit-md sm:flex-row">
                        <ActionLink
                            href="/contact"
                            className="px-unit-xl py-unit-md text-lg"
                        >
                            {translateText(translations, "services.cta.quote", locale, {
                                ka: "მოითხოვეთ შეთავაზება",
                                en: "Request an offer",
                                ru: "Запросить предложение",
                            })}
                        </ActionLink>
                        {contact.phone ? (
                            <ActionLink
                                href={toPhoneHref(contact.phone)}
                                variant="glass"
                                className="px-unit-xl py-unit-md text-lg"
                            >
                                {translateText(translations, "services.cta.call", locale, {
                                    ka: "დაგვირეკეთ",
                                    en: "Call us",
                                    ru: "Позвоните нам",
                                })}: {contact.phone}
                            </ActionLink>
                        ) : null}
                    </div>
                </div>
            </div>
        </section>
    );
}
