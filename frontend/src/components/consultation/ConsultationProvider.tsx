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
        null,
    );
    const title = translateText(
        translations,
        "consultation.modal.title",
        locale,
        null,
    );
    const description = translateText(
        translations,
        "consultation.modal.description",
        locale,
        null,
    );
    const closeLabel = translateText(
        translations,
        "consultation.modal.close",
        locale,
        null,
    );

    return (
        <>
            {children}
            <div
                aria-labelledby={title ? "consultation-title" : undefined}
                className="m-auto max-h-[90dvh] w-[min(92vw,42rem)] overflow-y-auto rounded-3xl border border-outline-variant/30 bg-surface-container p-0 text-on-surface shadow-2xl backdrop:bg-black/75 backdrop:backdrop-blur-sm"
                id={CONSULTATION_POPOVER_ID}
                popover="auto"
            >
                <div className="relative p-unit-lg sm:p-unit-xl">
                    <button
                        aria-label={closeLabel || undefined}
                        className="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant/30 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-white"
                        popoverTarget={CONSULTATION_POPOVER_ID}
                        popoverTargetAction="hide"
                        type="button"
                    >
                        <span aria-hidden="true" className="text-2xl">
                            &times;
                        </span>
                    </button>

                    {eyebrow || title || description ? (
                        <header className="mb-unit-lg pr-10">
                            {eyebrow ? (
                                <p className="mb-2 font-mono-sm text-mono-sm uppercase tracking-widest text-secondary">
                                    {eyebrow}
                                </p>
                            ) : null}
                            {title ? (
                                <h2
                                    className="font-headline-lg text-headline-lg text-white"
                                    id="consultation-title"
                                >
                                    {title}
                                </h2>
                            ) : null}
                            {description ? (
                                <p className="mt-3 leading-relaxed text-on-surface-variant">
                                    {description}
                                </p>
                            ) : null}
                        </header>
                    ) : null}
                    <ConsultationFormSlot />
                </div>
            </div>
        </>
    );
}
