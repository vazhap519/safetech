"use client";

import dynamic from "next/dynamic";

type FloatingWhatsAppSlotProps = {
    phone?: string;
    message?: string;
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
