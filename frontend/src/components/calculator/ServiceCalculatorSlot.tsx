"use client";

import dynamic from "next/dynamic";
import { useEffect, useRef, useState } from "react";

import { useLocalization } from "@/components/providers/LocalizationProvider";
import { buildPublicApiUrl } from "@/lib/public-api";
import type { CalculatorProfile } from "@/lib/service-calculator";

const ServiceCalculator = dynamic(() => import("./ServiceCalculator"), {
    loading: () => (
        <div
            aria-busy="true"
            aria-label="Loading service calculator"
            className="mt-unit-xl min-h-96 rounded-3xl border border-outline-variant/20 bg-surface-container-low"
        />
    ),
    ssr: false,
});

export default function ServiceCalculatorSlot({
    initialService,
}: {
    initialService?: string;
}) {
    const { locale, t } = useLocalization();
    const containerRef = useRef<HTMLDivElement>(null);
    const [shouldLoad, setShouldLoad] = useState(false);
    const [profiles, setProfiles] = useState<CalculatorProfile[]>([]);
    const [status, setStatus] = useState<"idle" | "loading" | "ready" | "error">(
        "idle",
    );
    const [reloadKey, setReloadKey] = useState(0);

    useEffect(() => {
        const target = containerRef.current;
        if (!target) return;

        const Observer = window.IntersectionObserver;

        if (typeof Observer !== "function") {
            const fallback = globalThis.setTimeout(() => setShouldLoad(true), 0);

            return () => globalThis.clearTimeout(fallback);
        }

        const observer = new Observer(
            ([entry]) => {
                if (!entry?.isIntersecting) return;
                setShouldLoad(true);
                observer.disconnect();
            },
            { rootMargin: "400px 0px" },
        );

        observer.observe(target);

        return () => observer.disconnect();
    }, []);

    useEffect(() => {
        if (!shouldLoad) return;

        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 20_000);
        let cancelled = false;

        const url = new URL(buildPublicApiUrl("/service-calculator/profiles"));
        url.searchParams.set("locale", locale);

        void Promise.resolve()
            .then(() => {
                if (cancelled) return null;

                setStatus("loading");
                setProfiles([]);

                return fetch(url, {
                    headers: { Accept: "application/json" },
                    signal: controller.signal,
                });
            })
            .then(async (response) => {
                if (!response) return;

                if (!response.ok) {
                    throw new Error(`Calculator profiles returned ${response.status}`);
                }

                const payload = (await response.json()) as
                    | CalculatorProfile[]
                    | { data?: CalculatorProfile[] };
                const items = Array.isArray(payload) ? payload : payload.data;
                const validProfiles = Array.isArray(items)
                    ? items.filter((profile) => Boolean(profile?.slug && profile?.name))
                    : [];

                if (!validProfiles.length) {
                    throw new Error("Calculator profiles are empty");
                }

                if (!cancelled) {
                    setProfiles(validProfiles);
                    setStatus("ready");
                }
            })
            .catch(() => {
                if (!cancelled) setStatus("error");
            })
            .finally(() => window.clearTimeout(timeout));

        return () => {
            cancelled = true;
            window.clearTimeout(timeout);
            controller.abort();
        };
    }, [locale, reloadKey, shouldLoad]);

    const loadingLabel = t("calculator.loading", {
        ka: "კალკულატორი იტვირთება",
        en: "Loading calculator",
        ru: "Калькулятор загружается",
    });
    const errorTitle = t("calculator.loadingError.title", {
        ka: "კალკულატორი დროებით ვერ ჩაიტვირთა",
        en: "The calculator could not load",
        ru: "Не удалось загрузить калькулятор",
    });
    const retryLabel = t("calculator.loadingError.retry", {
        ka: "თავიდან ცდა",
        en: "Try again",
        ru: "Повторить",
    });

    return (
        <div
            className="scroll-mt-28"
            id="service-calculator"
            ref={containerRef}
        >
            {status === "ready" ? (
                <ServiceCalculator
                    initialService={initialService}
                    profiles={profiles}
                />
            ) : status === "error" ? (
                <div className="mt-unit-xl rounded-3xl border border-outline-variant/20 bg-surface-container-low p-6 text-center sm:p-8">
                    <p className="font-semibold text-on-surface">{errorTitle}</p>
                    <button
                        className="mt-4 min-h-11 rounded-xl bg-primary px-5 font-semibold text-on-primary transition hover:brightness-110"
                        onClick={() => setReloadKey((value) => value + 1)}
                        type="button"
                    >
                        {retryLabel}
                    </button>
                </div>
            ) : (
                <div
                    aria-busy={status === "loading"}
                    aria-label={loadingLabel}
                    className="mt-unit-xl min-h-96 rounded-3xl border border-outline-variant/20 bg-surface-container-low"
                />
            )}
        </div>
    );
}
