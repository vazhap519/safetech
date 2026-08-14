import Image from "next/image";

import JsonLd from "@/components/seo/JsonLd";
import type { BackendPage } from "@/lib/backend";
import type { Locale } from "@/lib/locales";
import { absoluteLocalizedUrl } from "@/lib/seo";

export default function DynamicPage({
    page,
    locale,
}: {
    page: BackendPage;
    locale: Locale;
}) {
    const url = absoluteLocalizedUrl(`/pages/${page.slug}`, locale);
    const paragraphs = page.content
        .split(/\n\s*\n/)
        .map((item) => item.trim())
        .filter(Boolean);

    return (
        <>
            <JsonLd
                data={{
                    "@context": "https://schema.org",
                    "@type": "WebPage",
                    name: page.seo?.title || page.title,
                    description:
                        page.seo?.description || page.excerpt || page.content,
                    url,
                    ...(page.coverImage
                        ? { primaryImageOfPage: page.coverImage }
                        : {}),
                }}
            />
            <div className="bg-surface pb-16 pt-28 sm:pt-32">
                <article className="mx-auto w-full max-w-4xl px-5 sm:px-6 lg:px-8">
                    <header className="rounded-3xl border border-white/10 bg-surface-container/70 p-6 shadow-sm sm:p-10">
                        <h1 className="font-headline-md text-3xl font-bold tracking-tight text-on-surface sm:text-5xl">
                            {page.title}
                        </h1>
                        {page.excerpt ? (
                            <p className="mt-5 max-w-3xl text-base leading-7 text-on-surface-variant sm:text-lg">
                                {page.excerpt}
                            </p>
                        ) : null}
                    </header>
                    {page.coverImage ? (
                        <div className="relative mt-8 aspect-[16/9] overflow-hidden rounded-3xl border border-white/10 bg-surface-container">
                            <Image
                                alt={page.title}
                                className="object-cover"
                                fill
                                priority
                                sizes="(max-width: 896px) 100vw, 896px"
                                src={page.coverImage}
                            />
                        </div>
                    ) : null}
                    <div className="mt-8 rounded-3xl border border-white/10 bg-surface-container/50 p-6 text-base leading-8 text-on-surface-variant sm:p-10 sm:text-lg">
                        {paragraphs.map((paragraph, index) => (
                            <p
                                className={index === 0 ? "" : "mt-6"}
                                key={`${index}-${paragraph.slice(0, 30)}`}
                            >
                                {paragraph}
                            </p>
                        ))}
                    </div>
                </article>
            </div>
        </>
    );
}
