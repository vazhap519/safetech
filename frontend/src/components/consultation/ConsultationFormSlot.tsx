"use client";

import {
    useEffect,
    useState,
    type ComponentType,
} from "react";

import { CONSULTATION_POPOVER_ID } from "@/components/consultation/constants";

type ConsultationFormComponent = ComponentType;

export default function ConsultationFormSlot() {
    const [Form, setForm] = useState<ConsultationFormComponent | null>(null);

    useEffect(() => {
        const element = document.getElementById(CONSULTATION_POPOVER_ID);
        if (!element) return;

        const popover = element;
        let cancelled = false;
        let loading = false;

        async function loadForm() {
            if (Form || loading) return;
            loading = true;

            const loadedForm = await import(
                "@/components/consultation/ConsultationForm"
            );

            if (!cancelled) {
                setForm(() => loadedForm.default);
            }
        }

        function handleToggle() {
            if (popover.matches(":popover-open")) {
                void loadForm();
            }
        }

        if (popover.matches(":popover-open")) {
            void loadForm();
        }

        popover.addEventListener("toggle", handleToggle);

        return () => {
            cancelled = true;
            popover.removeEventListener("toggle", handleToggle);
        };
    }, [Form]);

    return Form ? (
        <Form />
    ) : (
        <div
            aria-hidden="true"
            className="min-h-48 animate-pulse rounded-2xl bg-surface-container-high/40"
        />
    );
}
