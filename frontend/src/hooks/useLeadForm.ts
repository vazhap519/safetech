"use client";

import { FormEvent, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { trackEvent } from "@/lib/analytics";

type FormStatus = "idle" | "submitting" | "success" | "error";

export function useLeadForm(source: string) {
    const [status, setStatus] = useState<FormStatus>("idle");
    const [message, setMessage] = useState("");
    const { locale, t } = useLocalization();

    async function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setStatus("submitting");
        setMessage("");

        const form = event.currentTarget;
        const payload = Object.fromEntries(
            new FormData(form).entries(),
        ) as Record<string, FormDataEntryValue>;
        const normalizedPayload = Object.fromEntries(
            Object.entries(payload).map(([key, value]) => [
                key,
                typeof value === "string" ? value.trim() : value,
            ]),
        );

        const details = Object.entries(normalizedPayload)
            .filter(([key]) => key.startsWith("details__"))
            .map(([key, value]) => {
                const detailKey = key.replace("details__", "");

                return {
                    key: detailKey,
                    label: String(
                        normalizedPayload[`details_label__${detailKey}`] ??
                            detailKey,
                    ),
                    type: String(
                        normalizedPayload[`details_type__${detailKey}`] ??
                            "text",
                    ),
                    value: typeof value === "string" ? value.trim() : String(value),
                };
            })
            .filter((detail) => detail.value !== "");

        const cleanedPayload = Object.fromEntries(
            Object.entries(normalizedPayload).filter(
                ([key]) =>
                    !key.startsWith("details__") &&
                    !key.startsWith("details_label__") &&
                    !key.startsWith("details_type__"),
            ),
        );

        const email = String(cleanedPayload.email ?? "");
        const phone = String(cleanedPayload.phone ?? "");

        if (!email && !phone) {
            setStatus("error");
            setMessage(t("forms.validation.contact", null));
            return;
        }

        try {
            const response = await fetch("/api/contact-leads", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    ...cleanedPayload,
                    email,
                    phone,
                    details,
                    locale,
                    source,
                }),
                signal: AbortSignal.timeout(15000),
            });
            const result = (await response.json()) as {
                message?: string;
                errors?: Record<string, string[]>;
            };

            const validationMessage = result.errors
                ? Object.values(result.errors).flat()[0]
                : undefined;

            if (!response.ok) {
                throw new Error(
                    validationMessage ||
                        result.message ||
                        t("forms.error.submit", null),
                );
            }

            form.reset();
            setStatus("success");
            setMessage(result.message || t("forms.success.submit", null));
            trackEvent("generate_lead", { form_source: source });
        } catch (error) {
            setStatus("error");
            setMessage(
                error instanceof Error && error.message !== "Failed to fetch"
                    ? error.message
                    : t("forms.error.network", null),
            );
        }
    }

    return { status, message, submit };
}
