"use client";

import dynamic from "next/dynamic";

type FloatingWhatsAppSlotProps = {
    phone?: string;
    message?: string;
};

const aiAssistantEnabled =
    process.env.NEXT_PUBLIC_AI_ASSISTANT_ENABLED === "true";

const SafeTechAssistant = dynamic(
    () => import("@/components/ai/SafeTechAssistant"),
    {
        ssr: false,
    },
);

const FloatingWhatsApp = dynamic(
    () => import("@/components/Contact/FloatingWhatsApp"),
    {
        ssr: false,
    },
);

export default function FloatingWhatsAppSlot(
    props: FloatingWhatsAppSlotProps,
) {
    return (
        <>
            {aiAssistantEnabled ? <SafeTechAssistant /> : null}
            <FloatingWhatsApp {...props} />
        </>
    );
}
