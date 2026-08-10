"use client";

import {
    useEffect,
    useState,
    type ComponentType,
} from "react";

type ServiceOption = {
    slug: string;
    label: string;
};

type ConsultationFormComponent = ComponentType<{
    serviceOptions: ServiceOption[];
}>;

export default function ConsultationFormSlot({
    serviceOptions,
}: {
    serviceOptions: ServiceOption[];
}) {
    const [Form, setForm] = useState<ConsultationFormComponent | null>(null);

    useEffect(() => {
        let cancelled = false;

        async function loadForm() {
            try {
                const loadedForm = await import(
                    "@/components/consultation/ConsultationForm"
                );

                if (!cancelled) {
                    setForm(() => loadedForm.default);
                }
            } catch {
                // Keep the lightweight placeholder if the chunk cannot be loaded.
            }
        }

        void loadForm();

        return () => {
            cancelled = true;
        };
    }, []);

    return Form ? (
        <Form serviceOptions={serviceOptions} />
    ) : (
        <div
            aria-hidden="true"
            className="min-h-48 animate-pulse rounded-2xl bg-surface-container-high/40"
        />
    );
}
