import ActionLink from "@/components/ui/ActionLink";
import Typography from "@/components/ui/Typography";
import { getSiteSettings } from "@/lib/site-settings";
import { createTranslator } from "@/lib/translations";

export default async function CtaSection() {
    const { locale, translations } = await getSiteSettings();
    const t = createTranslator(translations, locale);

    return (
        <section className="relative overflow-hidden px-margin-desktop py-unit-xl">
            <div className="absolute inset-0 mesh-gradient opacity-30" />
            <div className="relative z-10 mx-auto max-w-container-max rounded-3xl border-primary/20 px-unit-lg py-unit-xl text-center glass-card sm:px-unit-xl">
                <Typography as="h2" variant="section-title">
                    {t("about.cta.title", {
                        ka: "მზად ხართ უსაფრთხო ინფრასტრუქტურისთვის?",
                        en: "Ready for secure infrastructure?",
                        ru: "Готовы к безопасной инфраструктуре?",
                    })}
                </Typography>

                <Typography as="p" variant="section-description">
                    {t("about.cta.description", {
                        ka: "დაიწყეთ თქვენი ბიზნესის ტექნოლოგიური ტრანსფორმაცია დღესვე. ჩვენი ექსპერტები დაგეხმარებიან საუკეთესო არქიტექტურის შერჩევაში.",
                        en: "Start your business technology transformation today. Our experts will help you choose the right architecture.",
                        ru: "Начните технологическую трансформацию бизнеса уже сегодня. Наши эксперты помогут выбрать правильную архитектуру.",
                    })}
                </Typography>
                <ActionLink href="/contact" className="mt-unit-lg px-8 py-4">
                    {t("about.cta.button", {
                        ka: "მოთხოვნა",
                        en: "Send request",
                        ru: "Оставить заявку",
                    })}
                </ActionLink>
            </div>
        </section>
    );
}
