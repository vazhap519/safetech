"use client";

import { useEffect, useState } from "react";

import ConsultationFormSlot from "@/components/consultation/ConsultationFormSlot";
import {
    CONSULTATION_CLOSE_EVENT,
    CONSULTATION_MODAL_ID,
    CONSULTATION_OPEN_EVENT,
} from "@/components/consultation/constants";
import { trackConsultationOpen } from "@/lib/analytics-events";
import { trackEvent } from "@/lib/analytics";

type ServiceOption = {
    slug: string;
    label: string;
};

type ConsultationModalProps = {
    serviceOptions: ServiceOption[];
    eyebrow: string | null;
    title: string | null;
    description: string | null;
    closeLabel: string | null;
};

export default function ConsultationModal({
    serviceOptions,
    eyebrow,
    title,
    description,
    closeLabel,
}: ConsultationModalProps) {
    const [open, setOpen] = useState(false);

    useEffect(() => {
        function handleOpen() {
            setOpen(true);
            trackEvent("consultation_open");
            trackConsultationOpen();
        }

        function handleClose() {
            setOpen(false);
        }

        window.addEventListener(CONSULTATION_OPEN_EVENT, handleOpen);
        window.addEventListener(CONSULTATION_CLOSE_EVENT, handleClose);

        return () => {
            window.removeEventListener(CONSULTATION_OPEN_EVENT, handleOpen);
            window.removeEventListener(CONSULTATION_CLOSE_EVENT, handleClose);
        };
    }, []);

    useEffect(() => {
        if (!open) return;

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";

        function handleKeyDown(event: KeyboardEvent) {
            if (event.key === "Escape") {
                setOpen(false);
            }
        }

        window.addEventListener("keydown", handleKeyDown);

        return () => {
            document.body.style.overflow = previousOverflow;
            window.removeEventListener("keydown", handleKeyDown);
        };
    }, [open]);

    if (!open) return null;

    return (
        <div
            className="fixed inset-0 z-[100] flex items-end justify-center bg-black/75 p-0 backdrop-blur-sm sm:items-center sm:p-4"
            onMouseDown={(event) => {
                if (event.target === event.currentTarget) {
                    setOpen(false);
                }
            }}
        >
            <section
                aria-labelledby={title ? "consultation-title" : undefined}
                aria-modal="true"
                className="max-h-[100dvh] w-full overscroll-contain overflow-y-auto rounded-t-3xl border border-outline-variant/30 bg-surface-container text-on-surface shadow-2xl sm:max-h-[90dvh] sm:w-[min(92vw,42rem)] sm:rounded-3xl"
                id={CONSULTATION_MODAL_ID}
                role="dialog"
            >
                <div className="relative px-4 pb-[max(1rem,env(safe-area-inset-bottom))] pt-6 sm:p-unit-xl">
                    <button
                        aria-label={closeLabel || undefined}
                        className="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant/30 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-white"
                        onClick={() => setOpen(false)}
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

                    <ConsultationFormSlot serviceOptions={serviceOptions} />
                </div>
            </section>
        </div>
    );
}
