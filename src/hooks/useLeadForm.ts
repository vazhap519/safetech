"use client";

import { FormEvent, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { trackEvent } from "@/lib/analytics";

type FormStatus = "idle" | "submitting" | "success" | "error";

export function useLeadForm(source: string) {
    const [status, setStatus] = useState<FormStatus>("idle");
    const [message, setMessage] = useState("");
    const { t } = useLocalization();

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
        const email = String(normalizedPayload.email ?? "");
        const phone = String(normalizedPayload.phone ?? "");

        if (!email && !phone) {
            setStatus("error");
            setMessage(
                t("forms.validation.contact", {
                    ka: "მიუთითეთ ელფოსტა ან ტელეფონის ნომერი.",
                    en: "Please provide an email address or phone number.",
                    ru: "Укажите email или номер телефона.",
                }),
            );
            return;
        }

        try {
            const apiBaseUrl = (
                process.env.NEXT_PUBLIC_API_URL || "http://localhost:8000/api"
            ).replace(/\/$/, "");
            const response = await fetch(`${apiBaseUrl}/contact-leads`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    ...normalizedPayload,
                    email,
                    phone,
                    source,
                }),
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
                        t("forms.error.submit", {
                            ka: "მოთხოვნის გაგზავნა ვერ მოხერხდა.",
                            en: "We couldn't send your request.",
                            ru: "Не удалось отправить запрос.",
                        }),
                );
            }

            form.reset();
            setStatus("success");
            setMessage(
                result.message ||
                    t("forms.success.submit", {
                        ka: "მადლობა! მოთხოვნა წარმატებით გაიგზავნა.",
                        en: "Thank you! Your request has been sent successfully.",
                        ru: "Спасибо! Ваш запрос успешно отправлен.",
                    }),
            );
            trackEvent("generate_lead", { form_source: source });
        } catch (error) {
            setStatus("error");
            setMessage(
                error instanceof Error && error.message !== "Failed to fetch"
                    ? error.message
                    : t("forms.error.network", {
                          ka: "სერვერთან დაკავშირება ვერ მოხერხდა. სცადეთ ხელახლა.",
                          en: "We couldn't reach the server. Please try again.",
                          ru: "Не удалось связаться с сервером. Попробуйте снова.",
                      }),
            );
        }
    }

    return { status, message, submit };
}
