"use client";

import dynamic from "next/dynamic";

const ConsultationForm = dynamic(
    () => import("@/components/consultation/ConsultationForm"),
    {
        ssr: false,
        loading: () => null,
    },
);

export default function ConsultationFormSlot() {
    return <ConsultationForm />;
}
