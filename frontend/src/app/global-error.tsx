"use client";

function localeFromPathname() {
    if (typeof window === "undefined") return "ka";

    const firstSegment = window.location.pathname.split("/").filter(Boolean)[0];
    return firstSegment === "en" || firstSegment === "ru" ? firstSegment : "ka";
}

const copy = {
    ka: {
        title: "საიტის ჩატვირთვა ვერ მოხერხდა",
        description:
            "დროებითი ტექნიკური პრობლემა დაფიქსირდა. სცადეთ გვერდის ხელახლა ჩატვირთვა.",
        retry: "ხელახლა ცდა",
    },
    en: {
        title: "The site could not be loaded",
        description:
            "A temporary technical problem occurred. Please try loading the page again.",
        retry: "Try again",
    },
    ru: {
        title: "Не удалось загрузить сайт",
        description:
            "Возникла временная техническая проблема. Попробуйте загрузить страницу ещё раз.",
        retry: "Повторить",
    },
} as const;

export default function GlobalError({
    reset,
}: {
    error: Error & { digest?: string };
    reset: () => void;
}) {
    const locale = localeFromPathname();
    const text = copy[locale];

    return (
        <html lang={locale}>
            <body
                style={{
                    margin: 0,
                    background: "#070b14",
                    color: "#f5f7ff",
                    fontFamily: "system-ui, sans-serif",
                }}
            >
                <main
                    style={{
                        minHeight: "100vh",
                        display: "grid",
                        placeItems: "center",
                        padding: "24px",
                    }}
                >
                    <section
                        aria-live="polite"
                        style={{
                            width: "min(100%, 640px)",
                            border: "1px solid rgba(255,255,255,.14)",
                            borderRadius: "24px",
                            padding: "32px",
                            textAlign: "center",
                            background: "#111722",
                        }}
                    >
                        <p style={{ color: "#8fd3ff", margin: 0 }}>SafeTech</p>
                        <h1 style={{ margin: "16px 0 0", fontSize: "32px" }}>
                            {text.title}
                        </h1>
                        <p
                            style={{
                                margin: "16px auto 0",
                                maxWidth: "520px",
                                color: "#b7c0d4",
                                lineHeight: 1.6,
                            }}
                        >
                            {text.description}
                        </p>
                        <button
                            onClick={reset}
                            style={{
                                marginTop: "24px",
                                minHeight: "44px",
                                border: 0,
                                borderRadius: "12px",
                                padding: "12px 22px",
                                background: "#2f6df6",
                                color: "white",
                                fontWeight: 700,
                                cursor: "pointer",
                            }}
                            type="button"
                        >
                            {text.retry}
                        </button>
                    </section>
                </main>
            </body>
        </html>
    );
}
