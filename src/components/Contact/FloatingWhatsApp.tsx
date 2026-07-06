"use client";

import SocialIcon from "@/components/ui/SocialIcon";
import {
    getCurrentPagePath,
    trackWhatsAppClick,
} from "@/lib/analytics-events";

const DEFAULT_MESSAGE =
    "გამარჯობა, მაინტერესებს თქვენი მომსახურება.";

type FloatingWhatsAppProps = {
    phone?: string;
    message?: string;
};

function normalizePhone(phone?: string) {
    return phone?.replace(/[^\d]/g, "") || "";
}

export default function FloatingWhatsApp({
    phone,
    message = DEFAULT_MESSAGE,
}: FloatingWhatsAppProps) {
    const normalizedPhone = normalizePhone(phone);

    if (!normalizedPhone) return null;

    const href = `https://wa.me/${normalizedPhone}?text=${encodeURIComponent(message)}`;

    return (
        <a
            aria-label="WhatsApp-ით დაკავშირება"
            className="group fixed right-5 z-50 isolate grid size-14 place-items-center rounded-full bg-[#25D366] text-white shadow-[0_12px_35px_rgba(37,211,102,.4)] transition-transform hover:scale-110 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#25D366]/40 sm:right-7 sm:size-16"
            href={href}
            onClick={() => trackWhatsAppClick(getCurrentPagePath())}
            rel="noopener noreferrer"
            style={{ bottom: "max(1.25rem, env(safe-area-inset-bottom))" }}
            target="_blank"
            title="WhatsApp"
        >
            <span
                aria-hidden="true"
                className="absolute inset-0 -z-10 rounded-full bg-[#25D366]/50 motion-safe:animate-ping"
            />
            <span className="pointer-events-none absolute right-full mr-3 hidden whitespace-nowrap rounded-lg bg-surface-container-high px-3 py-2 text-sm font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100 sm:block">
                მოგვწერეთ WhatsApp-ზე
            </span>
            <SocialIcon className="size-7 sm:size-8" network="whatsapp" />
        </a>
    );
}
