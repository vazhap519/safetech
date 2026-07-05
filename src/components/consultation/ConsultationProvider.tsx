import type { ReactNode } from "react";
import ConsultationForm from "@/components/consultation/ConsultationForm";

export const CONSULTATION_POPOVER_ID = "consultation-popover";

export default function ConsultationProvider({ children }: { children: ReactNode }) {
    return (
        <>
            {children}
            <div
                aria-labelledby="consultation-title"
                className="m-auto w-[min(92vw,42rem)] max-h-[90dvh] overflow-y-auto rounded-3xl border border-outline-variant/30 bg-surface-container p-0 text-on-surface shadow-2xl backdrop:bg-black/75 backdrop:backdrop-blur-sm"
                id={CONSULTATION_POPOVER_ID}
                popover="auto"
            >
                <div className="relative p-unit-lg sm:p-unit-xl">
                    <button
                        aria-label="ფანჯრის დახურვა"
                        className="absolute right-4 top-4 flex h-10 w-10 items-center justify-center rounded-full border border-outline-variant/30 text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-white"
                        popoverTarget={CONSULTATION_POPOVER_ID}
                        popoverTargetAction="hide"
                        type="button"
                    >
                        <span aria-hidden="true" className="text-2xl">×</span>
                    </button>

                    <header className="mb-unit-lg pr-10">
                        <p className="mb-2 font-mono-sm text-mono-sm uppercase tracking-widest text-secondary">უფასო კონსულტაცია</p>
                        <h2 className="font-headline-lg text-headline-lg text-white" id="consultation-title">მოგვიყევით თქვენი პროექტის შესახებ</h2>
                        <p className="mt-3 leading-relaxed text-on-surface-variant">შეავსეთ მონაცემები და გააგრძელეთ მოთხოვნა საკონტაქტო გვერდზე.</p>
                    </header>

                    <ConsultationForm />
                </div>
            </div>
        </>
    );
}
