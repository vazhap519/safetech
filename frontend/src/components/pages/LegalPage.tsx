import JsonLd from "@/components/seo/JsonLd";
import type { BackendPage } from "@/lib/backend";
import type { Locale } from "@/lib/locales";
import { absoluteLocalizedUrl } from "@/lib/seo";

type LegalBlock =
    | { type: "heading"; level: 2 | 3; text: string }
    | { type: "paragraph"; text: string }
    | { type: "list"; ordered: boolean; items: string[] };

function parseLegalContent(content: string): LegalBlock[] {
    return content
        .split(/\n\s*\n/)
        .map((block) => block.trim())
        .filter(Boolean)
        .map((block): LegalBlock => {
            if (block.startsWith("### ")) {
                return { type: "heading", level: 3, text: block.slice(4).trim() };
            }

            if (block.startsWith("## ")) {
                return { type: "heading", level: 2, text: block.slice(3).trim() };
            }

            const lines = block
                .split("\n")
                .map((line) => line.trim())
                .filter(Boolean);

            if (lines.length && lines.every((line) => line.startsWith("- "))) {
                return {
                    type: "list",
                    ordered: false,
                    items: lines.map((line) => line.slice(2).trim()),
                };
            }

            if (lines.length && lines.every((line) => /^\d+\.\s/.test(line))) {
                return {
                    type: "list",
                    ordered: true,
                    items: lines.map((line) => line.replace(/^\d+\.\s*/, "").trim()),
                };
            }

            return { type: "paragraph", text: block.replace(/\n+/g, " ") };
        });
}

function updatedLabel(locale: Locale) {
    switch (locale) {
        case "en":
            return "Last updated";
        case "ru":
            return "Последнее обновление";
        default:
            return "ბოლო განახლება";
    }
}

function formatUpdatedAt(value: string | undefined, locale: Locale) {
    if (!value) return null;

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return null;

    const language = locale === "en" ? "en-GB" : locale === "ru" ? "ru-RU" : "ka-GE";

    return new Intl.DateTimeFormat(language, {
        day: "2-digit",
        month: "long",
        year: "numeric",
    }).format(date);
}

export default function LegalPage({
    page,
    locale,
    canonicalPath,
}: {
    page: BackendPage;
    locale: Locale;
    canonicalPath: "/privacy" | "/terms";
}) {
    const blocks = parseLegalContent(page.content);
    const updatedAt = formatUpdatedAt(page.updated_at, locale);
    const url = absoluteLocalizedUrl(canonicalPath, locale);

    return (
        <>
            <JsonLd
                data={{
                    "@context": "https://schema.org",
                    "@type": "WebPage",
                    name: page.seo?.title || page.title,
                    description: page.seo?.description || page.excerpt || page.content,
                    url,
                    ...(page.updated_at ? { dateModified: page.updated_at } : {}),
                }}
            />
            <div className="bg-surface pb-16 pt-28 sm:pb-20 sm:pt-32">
                <article className="mx-auto w-full max-w-4xl px-5 sm:px-6 lg:px-8">
                    <header className="rounded-3xl border border-outline-variant/15 bg-surface-container/70 p-6 shadow-sm sm:p-10">
                        <div className="max-w-3xl">
                            <p className="font-label-md text-sm font-semibold uppercase tracking-[0.16em] text-primary">
                                SafeTech
                            </p>
                            <h1 className="mt-3 font-headline-md text-3xl font-bold tracking-tight text-on-surface sm:text-5xl">
                                {page.title}
                            </h1>
                            {page.excerpt ? (
                                <p className="mt-5 text-base leading-7 text-on-surface-variant sm:text-lg">
                                    {page.excerpt}
                                </p>
                            ) : null}
                            {updatedAt ? (
                                <p className="mt-5 text-sm text-on-surface-variant/70">
                                    {updatedLabel(locale)}: {updatedAt}
                                </p>
                            ) : null}
                        </div>
                    </header>

                    <div className="mt-8 rounded-3xl border border-outline-variant/15 bg-surface-container/45 p-6 text-base leading-8 text-on-surface-variant sm:p-10 sm:text-lg">
                        {blocks.map((block, index) => {
                            if (block.type === "heading") {
                                return block.level === 2 ? (
                                    <h2
                                        className="mb-4 mt-9 text-2xl font-bold tracking-tight text-on-surface first:mt-0 sm:text-3xl"
                                        key={`${index}-${block.text}`}
                                    >
                                        {block.text}
                                    </h2>
                                ) : (
                                    <h3
                                        className="mb-3 mt-7 text-xl font-semibold text-on-surface sm:text-2xl"
                                        key={`${index}-${block.text}`}
                                    >
                                        {block.text}
                                    </h3>
                                );
                            }

                            if (block.type === "list") {
                                const ListTag = block.ordered ? "ol" : "ul";

                                return (
                                    <ListTag
                                        className={`my-5 space-y-2 pl-6 ${block.ordered ? "list-decimal" : "list-disc"}`}
                                        key={`${index}-${block.items[0] ?? "list"}`}
                                    >
                                        {block.items.map((item) => (
                                            <li key={item}>{item}</li>
                                        ))}
                                    </ListTag>
                                );
                            }

                            return (
                                <p className="my-5 first:mt-0" key={`${index}-${block.text.slice(0, 40)}`}>
                                    {block.text}
                                </p>
                            );
                        })}
                    </div>
                </article>
            </div>
        </>
    );
}
