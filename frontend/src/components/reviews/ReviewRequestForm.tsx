"use client";

import { useEffect, useMemo, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";

type InvitationDetails = {
    recipientName?: string | null;
    projectName?: string | null;
};

type RequestStatus = "loading" | "ready" | "submitting" | "success" | "error";

const copy = {
    ka: {
        eyebrow: "კლიენტის შეფასება",
        title: "გაგვიზიარეთ თქვენი გამოცდილება",
        description:
            "თქვენი გულწრფელი შეფასება სხვა ადამიანებს ეხმარება სწორი ტექნიკური პარტნიორის არჩევაში.",
        project: "დაკავშირებული სამუშაო",
        name: "სახელი და გვარი",
        company: "კომპანია / ობიექტი (სურვილისამებრ)",
        role: "თანამდებობა (სურვილისამებრ)",
        quote: "თქვენი შეფასება",
        quotePlaceholder: "რა გაკეთდა და როგორ შეაფასებდით მიღებულ შედეგს?",
        consent:
            "ვეთანხმები შეფასების გამოქვეყნებას SafeTech-ის ვებგვერდზე მხოლოდ ადმინისტრატორის დამტკიცების შემდეგ.",
        submit: "შეფასების გაგზავნა",
        submitting: "იგზავნება…",
        invalid:
            "ეს შეფასების ბმული აღარ არის აქტიური ან უკვე გამოყენებულია. გთხოვთ, დაუკავშირდით SafeTech-ს ახალი ბმულისთვის.",
        error: "შეფასების გაგზავნა ვერ მოხერხდა. სცადეთ ხელახლა ან დაუკავშირდით SafeTech-ს.",
        success:
            "გმადლობთ შეფასებისთვის! ის გამოქვეყნდება მხოლოდ ადმინისტრატორის შემოწმებისა და დამტკიცების შემდეგ.",
        thankYou: "მადლობა, რომ დაგვითმეთ დრო.",
    },
    en: {
        eyebrow: "Client feedback",
        title: "Tell us about your experience",
        description:
            "Your honest feedback helps other people choose the right technical partner.",
        project: "Related work",
        name: "Your name",
        company: "Company / property (optional)",
        role: "Role (optional)",
        quote: "Your review",
        quotePlaceholder: "What was completed, and how would you describe the result?",
        consent:
            "I agree that my review may be published on the SafeTech website only after administrator approval.",
        submit: "Send review",
        submitting: "Sending…",
        invalid:
            "This review link is no longer active or has already been used. Please contact SafeTech for a new link.",
        error: "We could not send your review. Please try again or contact SafeTech.",
        success:
            "Thank you for your feedback. It will be published only after an administrator reviews and approves it.",
        thankYou: "Thank you for taking the time.",
    },
    ru: {
        eyebrow: "Отзыв клиента",
        title: "Расскажите о вашем опыте",
        description:
            "Ваш честный отзыв помогает другим выбрать подходящего технического партнёра.",
        project: "Связанная работа",
        name: "Ваше имя",
        company: "Компания / объект (необязательно)",
        role: "Должность (необязательно)",
        quote: "Ваш отзыв",
        quotePlaceholder: "Что было выполнено и как вы оцениваете результат?",
        consent:
            "Я согласен, что мой отзыв может быть опубликован на сайте SafeTech только после одобрения администратора.",
        submit: "Отправить отзыв",
        submitting: "Отправка…",
        invalid:
            "Эта ссылка на отзыв больше не активна или уже была использована. Обратитесь в SafeTech за новой ссылкой.",
        error: "Не удалось отправить отзыв. Попробуйте ещё раз или свяжитесь с SafeTech.",
        success:
            "Спасибо за отзыв. Он будет опубликован только после проверки и одобрения администратора.",
        thankYou: "Спасибо, что уделили нам время.",
    },
} as const;

function getErrorMessage(payload: unknown) {
    if (
        payload &&
        typeof payload === "object" &&
        "errors" in payload &&
        payload.errors &&
        typeof payload.errors === "object"
    ) {
        const firstField = Object.values(payload.errors)[0];

        if (Array.isArray(firstField) && typeof firstField[0] === "string") {
            return firstField[0];
        }
    }

    return null;
}

export default function ReviewRequestForm({ token }: { token: string }) {
    const { locale } = useLocalization();
    const text = copy[locale];
    const endpoint = useMemo(
        () => `/api/review-invitations/${encodeURIComponent(token)}`,
        [token],
    );
    const [status, setStatus] = useState<RequestStatus>("loading");
    const [invitation, setInvitation] = useState<InvitationDetails | null>(null);
    const [message, setMessage] = useState("");

    useEffect(() => {
        const controller = new AbortController();

        async function loadInvitation() {
            try {
                const response = await fetch(endpoint, {
                    cache: "no-store",
                    signal: controller.signal,
                });

                if (!response.ok) {
                    throw new Error("invalid_invitation");
                }

                const payload = (await response.json()) as {
                    data?: InvitationDetails;
                };

                if (!controller.signal.aborted) {
                    setInvitation(payload.data ?? null);
                    setStatus("ready");
                }
            } catch {
                if (!controller.signal.aborted) {
                    setMessage(text.invalid);
                    setStatus("error");
                }
            }
        }

        void loadInvitation();

        return () => controller.abort();
    }, [endpoint, text.invalid]);

    async function submit(event: React.FormEvent<HTMLFormElement>) {
        event.preventDefault();
        setStatus("submitting");
        setMessage("");

        const form = event.currentTarget;
        const formData = new FormData(form);
        const payload = {
            author: String(formData.get("author") ?? "").trim(),
            company: String(formData.get("company") ?? "").trim(),
            role: String(formData.get("role") ?? "").trim(),
            quote: String(formData.get("quote") ?? "").trim(),
            consent: formData.get("consent") === "on",
            website: String(formData.get("website") ?? "").trim(),
        };

        try {
            const response = await fetch(`${endpoint}/submit`, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload),
                signal: AbortSignal.timeout(15000),
            });
            const result = (await response.json().catch(() => null)) as unknown;

            if (!response.ok) {
                throw new Error(getErrorMessage(result) || text.error);
            }

            setMessage(text.success);
            setStatus("success");
        } catch (error) {
            setMessage(error instanceof Error ? error.message : text.error);
            setStatus("ready");
        }
    }

    if (status === "loading") {
        return (
            <div
                aria-busy="true"
                className="mx-auto h-[34rem] max-w-2xl animate-pulse rounded-3xl border border-outline-variant/20 bg-surface-container-low"
            />
        );
    }

    if (status === "error") {
        return (
            <section className="mx-auto max-w-2xl rounded-3xl border border-error/30 bg-error/5 p-6 text-center sm:p-10">
                <p className="font-body-md leading-relaxed text-on-surface">{message}</p>
            </section>
        );
    }

    if (status === "success") {
        return (
            <section className="mx-auto max-w-2xl rounded-3xl border border-primary/30 bg-primary/5 p-6 text-center sm:p-10">
                <p className="font-headline-md text-xl text-on-surface">{text.thankYou}</p>
                <p className="mt-3 leading-relaxed text-on-surface-variant">{message}</p>
            </section>
        );
    }

    return (
        <section className="mx-auto max-w-2xl rounded-3xl border border-outline-variant/30 bg-surface-container-low p-5 shadow-2xl shadow-black/10 sm:p-8">
            <header className="border-b border-outline-variant/20 pb-6">
                <p className="font-mono-sm text-mono-sm font-semibold uppercase tracking-wider text-primary">
                    {text.eyebrow}
                </p>
                <h1 className="mt-3 font-display-lg text-[30px] font-semibold leading-tight text-on-surface sm:text-[38px]">
                    {text.title}
                </h1>
                <p className="mt-3 max-w-xl leading-relaxed text-on-surface-variant">
                    {text.description}
                </p>
                {invitation?.projectName ? (
                    <p className="mt-4 rounded-xl bg-surface-container-high px-4 py-3 text-sm text-on-surface-variant">
                        <span className="font-semibold text-on-surface">{text.project}: </span>
                        {invitation.projectName}
                    </p>
                ) : null}
            </header>

            <form className="mt-6 space-y-5" onSubmit={submit}>
                <input
                    aria-hidden="true"
                    autoComplete="off"
                    className="sr-only"
                    name="website"
                    tabIndex={-1}
                    type="text"
                />
                <label className="block space-y-2 font-label-md text-sm text-on-surface-variant">
                    <span>{text.name}</span>
                    <input
                        autoComplete="name"
                        className="w-full rounded-xl border border-outline-variant/30 bg-surface-container px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        defaultValue={invitation?.recipientName ?? ""}
                        name="author"
                        required
                        type="text"
                    />
                </label>
                <div className="grid gap-5 sm:grid-cols-2">
                    <label className="block space-y-2 font-label-md text-sm text-on-surface-variant">
                        <span>{text.company}</span>
                        <input
                            autoComplete="organization"
                            className="w-full rounded-xl border border-outline-variant/30 bg-surface-container px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            name="company"
                            type="text"
                        />
                    </label>
                    <label className="block space-y-2 font-label-md text-sm text-on-surface-variant">
                        <span>{text.role}</span>
                        <input
                            autoComplete="organization-title"
                            className="w-full rounded-xl border border-outline-variant/30 bg-surface-container px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                            name="role"
                            type="text"
                        />
                    </label>
                </div>
                <label className="block space-y-2 font-label-md text-sm text-on-surface-variant">
                    <span>{text.quote}</span>
                    <textarea
                        className="min-h-40 w-full resize-y rounded-xl border border-outline-variant/30 bg-surface-container px-4 py-3 text-on-surface outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/20"
                        name="quote"
                        placeholder={text.quotePlaceholder}
                        required
                        rows={6}
                    />
                </label>
                <label className="flex cursor-pointer items-start gap-3 text-sm leading-relaxed text-on-surface-variant">
                    <input
                        className="mt-1 size-4 accent-primary"
                        name="consent"
                        required
                        type="checkbox"
                    />
                    <span>{text.consent}</span>
                </label>
                <button
                    className="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-primary-container px-6 py-3 font-semibold text-on-primary-container shadow-lg shadow-blue-500/20 transition hover:brightness-110 disabled:cursor-wait disabled:opacity-60 sm:w-auto"
                    disabled={status === "submitting"}
                    type="submit"
                >
                    {status === "submitting" ? text.submitting : text.submit}
                </button>
                <p aria-live="polite" className="text-sm text-error" role="status">
                    {message}
                </p>
            </form>
        </section>
    );
}
