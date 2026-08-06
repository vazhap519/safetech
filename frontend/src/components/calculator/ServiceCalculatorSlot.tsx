"use client";

import dynamic from "next/dynamic";

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
    profiles,
}: {
    initialService?: string;
    profiles: CalculatorProfile[];
}) {
    return <ServiceCalculator initialService={initialService} profiles={profiles} />;
}
