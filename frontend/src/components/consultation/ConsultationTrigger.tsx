"use client";

import type { ButtonHTMLAttributes, MouseEvent, ReactNode } from "react";

import { CONSULTATION_OPEN_EVENT } from "@/components/consultation/constants";

type ConsultationTriggerProps = {
    children: ReactNode;
} & Omit<ButtonHTMLAttributes<HTMLButtonElement>, "type">;

export default function ConsultationTrigger({
    children,
    onClick,
    ...props
}: ConsultationTriggerProps) {
    function handleClick(event: MouseEvent<HTMLButtonElement>) {
        onClick?.(event);

        if (event.defaultPrevented) return;

        window.dispatchEvent(new Event(CONSULTATION_OPEN_EVENT));
    }

    return (
        <button type="button" {...props} onClick={handleClick}>
            {children}
        </button>
    );
}
