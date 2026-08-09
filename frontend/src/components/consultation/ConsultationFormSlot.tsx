"use client";

import {
    useEffect,
    useState,
    type ComponentType,
} from "react";

import { CONSULTATION_POPOVER_ID } from "@/components/consultation/constants";

type ServiceOption = {
    slug: string;
    label: string;
};

type ConsultationFormComponent = ComponentType<{
    serviceOptions: ServiceOption[];
}>;

type ToggleEventWithState = Event & {
    newState?: "open" | "closed";
};

export default function ConsultationFormSlot({
    serviceOptions,
}: {
    serviceOptions: ServiceOption[];
}) {
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

        function readPopoverOpenState(): boolean | null {
            try {
                return popover.matches(":popover-open");
            } catch {
                return null;
            }
        }

        function handleToggle(event: Event) {
            const nextState = (event as ToggleEventWithState).newState;

            if (nextState === "open") {
                void loadForm();
                return;
            }

            if (nextState == null && readPopoverOpenState() === true) {
                void loadForm();
            }
        }

        const initialOpenState = readPopoverOpenState();

        if (initialOpenState === true || initialOpenState === null) {
            // Some mobile browsers expose the popover API but reject the
            // :popover-open selector. In that case, eagerly loading the form
            // is safer than crashing the entire React tree.
            void loadForm();
        }

        popover.addEventListener("toggle", handleToggle);

        return () => {
            cancelled = true;
            popover.removeEventListener("toggle", handleToggle);
        };
    }, [Form]);

    return Form ? (
        <Form serviceOptions={serviceOptions} />
    ) : (
        <div
            aria-hidden="true"
            className="min-h-48 animate-pulse rounded-2xl bg-surface-container-high/40"
        />
    );
}
