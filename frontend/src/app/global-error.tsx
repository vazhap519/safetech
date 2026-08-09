"use client";

export default function GlobalError({
    error,
    reset,
}: {
    error: Error & { digest?: string };
    reset: () => void;
}) {
    const message = (error?.message || "Unknown client runtime error").slice(0, 500);
    const digest = error?.digest || "n/a";

    return (
        <html lang="ka">
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
                            საიტის ჩატვირთვა ვერ მოხერხდა
                        </h1>
                        <p
                            style={{
                                margin: "16px auto 0",
                                maxWidth: "520px",
                                color: "#b7c0d4",
                                lineHeight: 1.6,
                            }}
                        >
                            დროებითი ტექნიკური პრობლემა დაფიქსირდა. სცადეთ გვერდის
                            ხელახლა ჩატვირთვა.
                        </p>
                        <pre
                            style={{
                                margin: "20px 0 0",
                                padding: "14px",
                                borderRadius: "12px",
                                textAlign: "left",
                                whiteSpace: "pre-wrap",
                                overflowWrap: "anywhere",
                                background: "#0a0f18",
                                color: "#ffb4ab",
                                fontSize: "12px",
                                lineHeight: 1.5,
                            }}
                        >
                            {`Error: ${message}\nDigest: ${digest}`}
                        </pre>
                        <button
                            onClick={reset}
                            style={{
                                marginTop: "24px",
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
                            ხელახლა ცდა
                        </button>
                    </section>
                </main>
            </body>
        </html>
    );
}
