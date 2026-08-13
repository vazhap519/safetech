"use client";

import { FormEvent, useMemo, useRef, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { trackEvent } from "@/lib/analytics";
import { buildPublicApiUrl } from "@/lib/public-api";

type ChatMessage = {
    id?: string;
    role: "user" | "assistant";
    content: string;
};

type ChatResponse = {
    data?: {
        conversation_id?: string;
        message_id?: string;
        message?: string;
        lead_score?: number;
        lead_created?: boolean;
    };
    message?: string;
};

export default function SafeTechAssistant() {
    const { locale, t } = useLocalization();
    const [open, setOpen] = useState(false);
    const [conversationId, setConversationId] = useState<string | null>(null);
    const [input, setInput] = useState("");
    const [consent, setConsent] = useState(false);
    const [sending, setSending] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [feedback, setFeedback] = useState<Record<string, -1 | 1>>({});
    const [messages, setMessages] = useState<ChatMessage[]>(() => [
        {
            role: "assistant",
            content: t("ai.assistant.greeting", {
                ka: "გამარჯობა! მე ვარ SafeTech-ის AI კონსულტანტი. მითხარით რა სისტემის ან IT მომსახურების შერჩევაში დაგეხმაროთ.",
                en: "Hello! I’m SafeTech’s AI consultant. Tell me what security system or IT service you need help choosing.",
                ru: "Здравствуйте! Я AI-консультант SafeTech. Расскажите, какую систему безопасности или IT-услугу вы хотите подобрать.",
            }),
        },
    ]);
    const scrollRef = useRef<HTMLDivElement>(null);

    const labels = useMemo(
        () => ({
            title: t("ai.assistant.title", {
                ka: "SafeTech AI კონსულტანტი",
                en: "SafeTech AI Consultant",
                ru: "AI-консультант SafeTech",
            }),
            subtitle: t("ai.assistant.subtitle", {
                ka: "სერვისის შერჩევა • კონსულტაცია • შეთავაზება",
                en: "Service selection • Consultation • Quote",
                ru: "Подбор услуги • Консультация • Предложение",
            }),
            placeholder: t("ai.assistant.placeholder", {
                ka: "მაგ: მინდა 8 კამერა კერძო სახლში...",
                en: "For example: I need 8 cameras for a house...",
                ru: "Например: нужно 8 камер для частного дома...",
            }),
            consent: t("ai.assistant.consent", {
                ka: "ვეთანხმები ჩატის მონაცემების დამუშავებას კონსულტაციისა და მოთხოვნის დასამუშავებლად.",
                en: "I agree to processing chat data for consultation and handling my request.",
                ru: "Я согласен на обработку данных чата для консультации и обработки запроса.",
            }),
            consentRequired: t("ai.assistant.consentRequired", {
                ka: "ჩატის გასაგზავნად მონიშნეთ მონაცემების დამუშავებაზე თანხმობა.",
                en: "Please accept data processing before sending a chat message.",
                ru: "Перед отправкой сообщения подтвердите согласие на обработку данных.",
            }),
            send: t("ai.assistant.send", {
                ka: "გაგზავნა",
                en: "Send",
                ru: "Отправить",
            }),
            close: t("ai.assistant.close", {
                ka: "დახურვა",
                en: "Close",
                ru: "Закрыть",
            }),
            open: t("ai.assistant.open", {
                ka: "AI კონსულტანტის გახსნა",
                en: "Open AI consultant",
                ru: "Открыть AI-консультанта",
            }),
            typing: t("ai.assistant.typing", {
                ka: "პასუხს ვამზადებ...",
                en: "Preparing an answer...",
                ru: "Готовлю ответ...",
            }),
        }),
        [t],
    );

    async function sendMessage(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        const message = input.trim();

        if (!message || sending) return;
        if (!consent) {
            setError(labels.consentRequired);
            return;
        }

        setError(null);
        setInput("");
        setSending(true);
        setMessages((current) => [...current, { role: "user", content: message }]);
        trackEvent("ai_assistant_message", { action: "send" });

        try {
            const response = await fetch(buildPublicApiUrl("/ai/chat"), {
                method: "POST",
                headers: { "Content-Type": "application/json", Accept: "application/json" },
                body: JSON.stringify({
                    conversation_id: conversationId,
                    message,
                    locale,
                    privacy: true,
                    website: "",
                }),
            });
            const payload = (await response.json().catch(() => ({}))) as ChatResponse;
            const assistantText = payload.data?.message || payload.message;

            if (!response.ok || !assistantText) {
                throw new Error(assistantText || "AI assistant request failed");
            }

            if (payload.data?.conversation_id) {
                setConversationId(payload.data.conversation_id);
            }

            if (payload.data?.lead_created) {
                trackEvent("generate_lead", {
                    form_source: "ai_assistant",
                    lead_score: payload.data.lead_score,
                });
            }

            setMessages((current) => [
                ...current,
                {
                    id: payload.data?.message_id,
                    role: "assistant",
                    content: assistantText,
                },
            ]);
            window.setTimeout(() => {
                scrollRef.current?.scrollTo({ top: scrollRef.current.scrollHeight, behavior: "smooth" });
            }, 0);
        } catch (caught) {
            setError(
                caught instanceof Error
                    ? caught.message
                    : t("ai.assistant.error", {
                          ka: "AI კონსულტანტთან დაკავშირება ვერ მოხერხდა.",
                          en: "Could not reach the AI consultant.",
                          ru: "Не удалось связаться с AI-консультантом.",
                      }),
            );
        } finally {
            setSending(false);
        }
    }

    async function rateMessage(messageId: string, rating: -1 | 1) {
        if (feedback[messageId]) return;

        setFeedback((current) => ({ ...current, [messageId]: rating }));

        try {
            await fetch(buildPublicApiUrl(`/ai/messages/${encodeURIComponent(messageId)}/feedback`), {
                method: "POST",
                headers: { "Content-Type": "application/json", Accept: "application/json" },
                body: JSON.stringify({ rating }),
            });
        } catch {
            setFeedback((current) => {
                const next = { ...current };
                delete next[messageId];
                return next;
            });
        }
    }

    function toggleOpen() {
        setOpen((current) => {
            const next = !current;
            if (next) trackEvent("ai_assistant_open");
            return next;
        });
    }

    return (
        <div className="fixed bottom-24 right-4 z-[90] sm:right-6">
            {open ? (
                <section
                    aria-label={labels.title}
                    className="mb-3 flex h-[min(72vh,620px)] w-[min(calc(100vw-2rem),390px)] flex-col overflow-hidden rounded-3xl border border-outline-variant/30 bg-surface-container shadow-2xl shadow-black/40"
                >
                    <header className="flex items-start justify-between gap-3 border-b border-outline-variant/20 bg-surface-container-high px-4 py-4">
                        <div>
                            <p className="font-semibold text-on-surface">{labels.title}</p>
                            <p className="mt-1 text-xs text-on-surface-variant">{labels.subtitle}</p>
                        </div>
                        <button
                            type="button"
                            onClick={() => setOpen(false)}
                            className="rounded-xl px-3 py-2 text-sm text-on-surface-variant transition hover:bg-surface-container-highest hover:text-on-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                            aria-label={labels.close}
                        >
                            ×
                        </button>
                    </header>

                    <div ref={scrollRef} className="flex-1 space-y-3 overflow-y-auto px-4 py-4" aria-live="polite">
                        {messages.map((message, index) => (
                            <div key={`${message.role}-${message.id ?? index}`} className={message.role === "user" ? "ml-10" : "mr-6"}>
                                <div
                                    className={
                                        message.role === "user"
                                            ? "rounded-2xl rounded-br-md bg-primary-container px-4 py-3 text-sm leading-relaxed text-on-primary-container"
                                            : "rounded-2xl rounded-bl-md bg-surface-container-high px-4 py-3 text-sm leading-relaxed text-on-surface"
                                    }
                                >
                                    <p className="whitespace-pre-wrap">{message.content}</p>
                                </div>
                                {message.role === "assistant" && message.id ? (
                                    <div className="mt-1 flex gap-1 pl-2">
                                        <button
                                            type="button"
                                            onClick={() => rateMessage(message.id as string, 1)}
                                            className="rounded-lg px-2 py-1 text-xs text-on-surface-variant hover:bg-surface-container-high"
                                            aria-label="Helpful"
                                            aria-pressed={feedback[message.id] === 1}
                                        >
                                            👍
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => rateMessage(message.id as string, -1)}
                                            className="rounded-lg px-2 py-1 text-xs text-on-surface-variant hover:bg-surface-container-high"
                                            aria-label="Not helpful"
                                            aria-pressed={feedback[message.id] === -1}
                                        >
                                            👎
                                        </button>
                                    </div>
                                ) : null}
                            </div>
                        ))}
                        {sending ? <p className="text-xs text-on-surface-variant">{labels.typing}</p> : null}
                    </div>

                    <form onSubmit={sendMessage} className="border-t border-outline-variant/20 bg-surface-container-low px-4 py-4">
                        <label className="mb-3 flex cursor-pointer items-start gap-2 text-xs leading-relaxed text-on-surface-variant">
                            <input
                                type="checkbox"
                                checked={consent}
                                onChange={(event) => {
                                    setConsent(event.target.checked);
                                    if (event.target.checked) setError(null);
                                }}
                                className="mt-0.5 h-4 w-4 rounded border-outline accent-primary"
                            />
                            <span>{labels.consent}</span>
                        </label>
                        <div className="flex items-end gap-2">
                            <textarea
                                value={input}
                                onChange={(event) => setInput(event.target.value)}
                                placeholder={labels.placeholder}
                                rows={2}
                                maxLength={2000}
                                className="min-h-12 flex-1 resize-none rounded-2xl border border-outline-variant/40 bg-surface px-3 py-3 text-sm text-on-surface outline-none placeholder:text-on-surface-variant/60 focus:border-primary"
                            />
                            <button
                                type="submit"
                                disabled={sending || !input.trim()}
                                className="min-h-12 rounded-2xl bg-primary-container px-4 py-3 text-sm font-semibold text-on-primary-container transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {labels.send}
                            </button>
                        </div>
                        {error ? <p className="mt-2 text-xs text-error">{error}</p> : null}
                    </form>
                </section>
            ) : null}

            <button
                type="button"
                onClick={toggleOpen}
                className="ml-auto flex h-14 min-w-14 items-center justify-center rounded-full border border-primary/30 bg-primary-container px-4 font-semibold text-on-primary-container shadow-xl shadow-black/30 transition hover:scale-[1.03] hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-background"
                aria-expanded={open}
                aria-label={labels.open}
            >
                <span className="mr-2 inline-block h-2 w-2 rounded-full bg-green-400" aria-hidden="true" />
                AI
            </button>
        </div>
    );
}
