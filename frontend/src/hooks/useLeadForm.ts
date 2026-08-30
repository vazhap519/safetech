"use client";

import { FormEvent, useRef, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { trackLeadCreated } from "@/lib/analytics-events";
import {
    getLeadAttributionDetails,
    trackEvent,
} from "@/lib/analytics";

type FormStatus = "idle" | "submitting" | "success" | "error";

type LeadResponse = {
    message?: string;
    errors?: Record<string, string[]>;
};

type PendingSubmission = {
    fingerprint: string;
    idempotencyKey: string;
};

const SUBMISSION_STORAGE_PREFIX = "safetech:lead-submission:";

function newIdempotencyKey() {
    return globalThis.crypto?.randomUUID?.() ??
        `${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

function submissionStorageKey(source: string, fingerprint: string) {
    let hash = 2166136261;

    for (let index = 0; index < fingerprint.length; index += 1) {
        hash ^= fingerprint.charCodeAt(index);
        hash = Math.imul(hash, 16777619);
    }

    return `${SUBMISSION_STORAGE_PREFIX}${source}:${(hash >>> 0).toString(36)}`;
}

function readSubmissionKey(storageKey: string) {
    try {
        return globalThis.sessionStorage?.getItem(storageKey) || null;
    } catch {
        return null;
    }
}

function writeSubmissionKey(storageKey: string, value: string | null) {
    try {
        if (value) {
            globalThis.sessionStorage?.setItem(storageKey, value);
        } else {
            globalThis.sessionStorage?.removeItem(storageKey);
        }
    } catch {
        // Storage can be unavailable in privacy-restricted browsers. Database
        // idempotency still protects retries within the current page session.
    }
}

export function useLeadForm(source: string) {
    const [status, setStatus] = useState<FormStatus>("idle");
    const [message, setMessage] = useState("");
    const pendingSubmission = useRef<PendingSubmission | null>(null);
    const submitting = useRef(false);
    const { locale, t } = useLocalization();

    async function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        if (submitting.current) return;

        submitting.current = true;
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

        const formDetails = Object.entries(normalizedPayload)
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
                    value:
                        typeof value === "string" ? value.trim() : String(value),
                };
            })
            .filter((detail) => detail.value !== "");
        const details = [
            ...formDetails,
            ...getLeadAttributionDetails(),
        ].slice(0, 50);

        const cleanedPayload = Object.fromEntries(
            Object.entries(normalizedPayload).filter(
                ([key]) =>
                    !key.startsWith("details__") &&
                    !key.startsWith("details_label__") &&
                    !key.startsWith("details_type__"),
            ),
        );
        const leadMessage = String(
            cleanedPayload.message ?? cleanedPayload.details ?? "",
        ).trim();
        const firstName = String(cleanedPayload.firstName ?? "");
        const lastName = String(cleanedPayload.lastName ?? "");
        const email = String(cleanedPayload.email ?? "");
        const phone = String(cleanedPayload.phone ?? "");
        const address = String(cleanedPayload.address ?? "");
        const serviceSlug = String(
            cleanedPayload.serviceSlug ?? cleanedPayload.service_slug ?? "",
        ).trim();
        const privacyAccepted = cleanedPayload.privacy === "1";
        const missingRequiredContact =
            source === "consultation-popup" &&
            (!firstName ||
                !lastName ||
                !phone ||
                !email ||
                !address ||
                !serviceSlug ||
                !leadMessage ||
                !privacyAccepted);

        if (missingRequiredContact) {
            setStatus("error");
            setMessage(
                t("consultation.form.validation", {
                    ka: "შეავსეთ ყველა სავალდებულო ველი და დაეთანხმეთ მონაცემების დამუშავებას.",
                    en: "Complete every required field and accept data processing.",
                    ru: "Заполните все обязательные поля и подтвердите согласие на обработку данных.",
                }),
            );
            submitting.current = false;
            return;
        }

        const requestBody = {
            ...cleanedPayload,
            ...(email ? { email } : {}),
            phone,
            ...(leadMessage ? { message: leadMessage } : {}),
            details,
            locale,
            source,
        };
        const fingerprint = JSON.stringify(requestBody);

        const storageKey = submissionStorageKey(source, fingerprint);
        const storedIdempotencyKey = readSubmissionKey(storageKey);

        if (pendingSubmission.current?.fingerprint !== fingerprint) {
            pendingSubmission.current = {
                fingerprint,
                idempotencyKey: storedIdempotencyKey || newIdempotencyKey(),
            };
            writeSubmissionKey(
                storageKey,
                pendingSubmission.current.idempotencyKey,
            );
        }

        try {
            const response = await fetch("/api/contact-leads", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Idempotency-Key": pendingSubmission.current.idempotencyKey,
                },
                body: fingerprint,
                signal: AbortSignal.timeout(15000),
            });
            const result = (await response
                .json()
                .catch(() => null)) as LeadResponse | null;
            const validationMessage = result?.errors
                ? Object.values(result.errors).flat()[0]
                : undefined;

            if (!response.ok) {
                throw new Error(
                    validationMessage ||
                        result?.message ||
                        t("forms.error.submit", null),
                );
            }

            pendingSubmission.current = null;
            writeSubmissionKey(storageKey, null);
            form.reset();
            setStatus("success");
            setMessage(result?.message || t("forms.success.submit", null));

            trackEvent("generate_lead", {
                form_source: source,
                service_slug: serviceSlug || undefined,
            });
            trackLeadCreated(source, serviceSlug || undefined);
        } catch (error) {
            setStatus("error");

            const safeApiMessage =
                error instanceof Error &&
                ![
                    "AbortError",
                    "TimeoutError",
                    "TypeError",
                ].includes(error.name) &&
                error.message !== "Failed to fetch"
                    ? error.message
                    : "";

            setMessage(safeApiMessage || t("forms.error.network", null));
        } finally {
            submitting.current = false;
        }
    }

    return { status, message, submit };
}
