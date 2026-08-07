"use client";

import { useEffect } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import LocalizedLink from "@/components/ui/LocalizedLink";

export default function ErrorPage({
    error,
    reset,
}: {
    error: Error & { digest?: string };
    reset: () => void;
}) {
    const { t } = useLocalization();

    useEffect(() => {
        console.error(error);
    }, [error]);

    const title = t("error.title", {
        ka: "გვერდის ჩატვირთვა ვერ მოხერხდა",
        en: "We could not load this page",
        ru: "Не удалось загрузить страницу",
    });
    const description = t("error.description", {
        ka: "სცადეთ ხელახლა. თუ პრობლემა გაგრძელდება, დაბრუნდით მთავარ გვერდზე და მოგვიანებით სცადეთ.",
        en: "Try again. If the problem continues, return to the home page and try again later.",
        ru: "Попробуйте ещё раз. Если проблема сохранится, вернитесь на главную страницу и повторите попытку позже.",
    });
    const retryLabel = t("error.retry", {
        ka: "ხელახლა ცდა",
        en: "Try again",
        ru: "Повторить",
    });
    const homeLabel = t("error.home", {
        ka: "მთავარ გვერდზე დაბრუნება",
        en: "Return home",
        ru: "На главную",
    });

    return (
        <section className="flex min-h-[70svh] items-center justify-center px-5 py-24 sm:px-6 lg:px-8">
            <div className="w-full max-w-2xl rounded-3xl border border-outline-variant/30 bg-surface-container p-6 text-center shadow-2xl sm:p-10">
                <p className="font-mono-sm uppercase tracking-widest text-secondary">
                    SafeTech
                </p>
                <h1 className="mt-4 text-3xl font-semibold text-white sm:text-4xl">
                    {title}
                </h1>
                <p className="mx-auto mt-4 max-w-xl leading-relaxed text-on-surface-variant">
                    {description}
                </p>
                <div className="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <button
                        className="rounded-xl bg-primary-container px-6 py-3 font-semibold text-on-primary-container transition hover:brightness-110"
                        onClick={reset}
                        type="button"
                    >
                        {retryLabel}
                    </button>
                    <LocalizedLink
                        className="rounded-xl border border-outline-variant/40 px-6 py-3 font-semibold text-on-surface transition hover:bg-surface-container-high"
                        href="/"
                    >
                        {homeLabel}
                    </LocalizedLink>
                </div>
            </div>
        </section>
    );
}
