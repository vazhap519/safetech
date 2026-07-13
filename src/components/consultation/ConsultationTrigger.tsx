import type { ButtonHTMLAttributes, ReactNode } from "react";
import { CONSULTATION_POPOVER_ID } from "@/components/consultation/constants";

type ConsultationTriggerProps = {
    children: ReactNode;
} & Omit<ButtonHTMLAttributes<HTMLButtonElement>, "type">;

export default function ConsultationTrigger({ children, ...props }: ConsultationTriggerProps) {
    return <button popoverTarget={CONSULTATION_POPOVER_ID} type="button" {...props}>{children}</button>;
}
