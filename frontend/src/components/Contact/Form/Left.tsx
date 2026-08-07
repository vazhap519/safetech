"use client";

import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Typography from "@/components/ui/Typography";

export default function Left() {
    const { t } = useLocalization();
    const title = t("contact.form.title", {
        ka: "მიიღეთ ტექნიკური კონსულტაცია",
        en: "Get a technical consultation",
        ru: "Получите техническую консультацию",
    });
    const description = t("contact.intro.paragraph.0", {
        ka: "მოკლედ აღწერეთ ობიექტი, პრობლემა ან სასურველი სისტემა. საჭიროების შემთხვევაში დაგისვამთ დამაზუსტებელ კითხვებს.",
        en: "Briefly describe the property, issue, or desired system. We will ask follow-up questions when needed.",
        ru: "Кратко опишите объект, проблему или желаемую систему. При необходимости зададим уточняющие вопросы.",
    });
    const note = t("consultation.cta.note", {
        ka: "კონსულტაციის ფორმა ერთ ფანჯარაში გაიხსნება. შეავსეთ ყველა სავალდებულო ველი და ჩვენ დაგიკავშირდებით.",
        en: "The consultation form opens in one window. Complete all required fields and we will contact you.",
        ru: "Форма консультации откроется в одном окне. Заполните все обязательные поля, и мы свяжемся с вами.",
    });
    const ctaLabel = t("home.cta.submit", {
        ka: "კონსულტაციის მიღება",
        en: "Get consultation",
        ru: "Получить консультацию",
    });

    return (
        <div className="glass-panel relative overflow-hidden rounded-2xl p-unit-md sm:p-unit-lg lg:col-span-7">
            <div
                aria-hidden="true"
                className="absolute left-0 top-0 h-1 w-full bg-gradient-to-r from-primary to-secondary opacity-50"
            />

            {title ? (
                <Typography as="h3" className="mb-unit-md" variant="section-title">
                    {title}
                </Typography>
            ) : null}

            {description ? (
                <p className="max-w-2xl leading-relaxed text-on-surface-variant">
                    {description}
                </p>
            ) : null}

            {note ? (
                <p className="mt-unit-md rounded-2xl border border-outline-variant/30 bg-surface-container-low/40 px-4 py-3 text-sm leading-relaxed text-on-surface-variant">
                    {note}
                </p>
            ) : null}

            {ctaLabel ? (
                <ConsultationTrigger className="mt-unit-lg inline-flex min-h-12 items-center justify-center rounded-xl bg-primary-container px-6 py-3 font-semibold text-on-primary-container shadow-lg shadow-blue-500/20 transition hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface">
                    {ctaLabel}
                </ConsultationTrigger>
            ) : null}
        </div>
    );
}
