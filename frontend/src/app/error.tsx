"use client";

import Link from "next/link";
import { useEffect } from "react";

export default function ErrorPage({
    error,
    reset,
}: {
    error: Error & { digest?: string };
    reset: () => void;
}) {
    useEffect(() => {
        console.error(error);
    }, [error]);

    return (
        <section className="flex min-h-[70svh] items-center justify-center px-5 py-24 sm:px-6 lg:px-8">
            <div className="w-full max-w-2xl rounded-3xl border border-outline-variant/30 bg-surface-container p-6 text-center shadow-2xl sm:p-10">
                <p className="font-mono-sm uppercase tracking-widest text-secondary">
                    SafeTech
                </p>
                <h1 className="mt-4 text-3xl font-semibold text-white sm:text-4xl">
                    გვერდის ჩატვირთვა ვერ მოხერხდა
                </h1>
                <p className="mx-auto mt-4 max-w-xl leading-relaxed text-on-surface-variant">
                    სცადეთ ხელახლა. თუ პრობლემა გაგრძელდება, დაბრუნდით მთავარ გვერდზე და მოგვიანებით სცადეთ.
                </p>
                <div className="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <button
                        className="rounded-xl bg-primary-container px-6 py-3 font-semibold text-on-primary-container transition hover:brightness-110"
                        onClick={reset}
                        type="button"
                    >
                        ხელახლა ცდა
                    </button>
                    <Link
                        className="rounded-xl border border-outline-variant/40 px-6 py-3 font-semibold text-on-surface transition hover:bg-surface-container-high"
                        href="/"
                    >
                        მთავარ გვერდზე დაბრუნება
                    </Link>
                </div>
            </div>
        </section>
    );
}
