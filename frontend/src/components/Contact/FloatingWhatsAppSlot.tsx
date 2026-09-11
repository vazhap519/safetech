"use client";

import dynamic from "next/dynamic";

type FloatingWhatsAppSlotProps = {
    callPhone?: string;
    message?: string;
    whatsappPhone?: string;
};

const FloatingWhatsApp = dynamic(
    () => import("@/components/Contact/FloatingWhatsApp"),
    {
        ssr: false,
    },
);

export default function FloatingWhatsAppSlot(
    props: FloatingWhatsAppSlotProps,
) {
    return <FloatingWhatsApp {...props} />;
}
