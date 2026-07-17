import type { ReactNode } from "react";

import { CONSULTATION_POPOVER_ID } from "@/components/consultation/constants";
import ConsultationFormSlot from "@/components/consultation/ConsultationFormSlot";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function ConsultationProvider({
    children,
}: {
    children: ReactNode;
}) {
    const { locale, translations } = await getSiteSettings();
    const eyebrow = translateText(
        translations,
        "consultation.modal.eyebrow",
        locale,
        {
            ka: "უფასო კონსულტაცია",
            en: "Free consultation",
            ru: "Бесплатная консультация",
        },
    );
    const title = translateText(
        translations,
        "consultation.modal.title",
        locale,
        {
            ka: "მოგვიყევით თქვენი პროექტის შესახებ",
            en: "Tell us about your project",
            ru: "Расскажите нам о вашем проекте",
        },
    );
    const description = translateText(
        translations,
        "consultation.modal.description",
        locale,
        {
            ka: "შეავსეთ მონაცემები და გავაგრძელებთ მოთხოვნას საკონტაქტო გვერდზე.",
            en: "Fill in the details and we will continue the request from the contact page.",
            ru: "Заполните данные, и мы продолжим обработку запроса со страницы контактов.",
        },
    );
    const closeLabel = translateText(
        translations,
        "consultation.modal.close",
        locale,
        {
            ka: "ფანჯრის დახურვა",
            en: "Close dialog",
            ru: "Закрыть окно",
        },
    );

    return (
        <>
            {children}
            <div
                aria-labelledby="consultation-title"
                className="m-auto max-h-[90dvh] w-[min(92vw,42rem)] overflow-y-auto rounded-3xl border border-outline-variant/30 bg-surface-container p-0 text-on-surface shadow-2xl backdrop:bg-black/75 backdrop:backdrop-blur-sm"
                id={CONSULTATION_POPOVER_ID}
                popover="auto"
            >
                <div className="relative p-unit-lg sm:p-unit-xl">
                    <button
                        aria-label={closeLabel}
                        className="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant/30 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-white"
                        popoverTarget={CONSULTATION_POPOVER_ID}
                        popoverTargetAction="hide"
                        type="button"
                    >
                        <span aria-hidden="true" className="text-2xl">
                            &times;
                        </span>
                    </button>

                    <header className="mb-unit-lg pr-10">
                        <p className="mb-2 font-mono-sm text-mono-sm uppercase tracking-widest text-secondary">
                            {eyebrow}
                        </p>
                        <h2
                            className="font-headline-lg text-headline-lg text-white"
                            id="consultation-title"
                        >
                            {title}
                        </h2>
                        <p className="mt-3 leading-relaxed text-on-surface-variant">
                            {description}
                        </p>
                    </header>
                    <ConsultationFormSlot />
                </div>
            </div>
        </>
    );
}
