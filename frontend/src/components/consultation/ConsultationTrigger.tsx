"use client";

import type { ButtonHTMLAttributes, MouseEvent, ReactNode } from "react";

import { CONSULTATION_OPEN_EVENT, type ConsultationPrefill } from "@/components/consultation/constants";

type ConsultationTriggerProps = {
    children: ReactNode;
    prefill?: ConsultationPrefill;
} & Omit<ButtonHTMLAttributes<HTMLButtonElement>, "type">;

export default function ConsultationTrigger({
    children,
    onClick,
    prefill,
    ...props
}: ConsultationTriggerProps) {
    function handleClick(event: MouseEvent<HTMLButtonElement>) {
        onClick?.(event);

        if (event.defaultPrevented) return;

        window.dispatchEvent(new CustomEvent(CONSULTATION_OPEN_EVENT, { detail: prefill }));
    }

    return (
        <button
            data-consultation-trigger=""
            type="button"
            {...props}
            onClick={handleClick}
        >
            {children}
        </button>
    );
}
