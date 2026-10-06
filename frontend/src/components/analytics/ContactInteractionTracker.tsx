"use client";

import { useEffect } from "react";

import {
    getCurrentPagePath,
    trackContactClick,
    trackWhatsAppClick,
} from "@/lib/analytics-events";
import { trackEvent } from "@/lib/analytics";

function contactMethod(
    href: string,
): "phone" | "email" | "whatsapp" | "messenger" | null {
    const normalizedHref = href.trim();

    if (normalizedHref.startsWith("tel:")) return "phone";

    if (normalizedHref.startsWith("mailto:")) {
        const recipient = normalizedHref.slice(7).split("?", 1)[0]?.trim();

        return recipient ? "email" : null;
    }

    try {
        const url = new URL(normalizedHref, window.location.origin);
        const isWhatsApp =
            url.hostname === "wa.me" ||
            url.hostname === "api.whatsapp.com" ||
            url.hostname === "web.whatsapp.com";
        const hasRecipient = /\d/.test(url.pathname);
        const isMessenger =
            url.hostname === "m.me" ||
            url.hostname === "messenger.com" ||
            url.hostname === "www.messenger.com";

        if (isWhatsApp && hasRecipient) return "whatsapp";
        if (isMessenger && url.pathname !== "/") return "messenger";

        return null;
    } catch {
        return null;
    }
}

export default function ContactInteractionTracker() {
    useEffect(() => {
        function handleClick(event: MouseEvent) {
            if (!(event.target instanceof Element)) return;

            const link = event.target.closest<HTMLAnchorElement>("a[href]");

            if (!link) return;

            const method = contactMethod(link.href);

            if (!method) return;

            if (method === "whatsapp") {
                trackWhatsAppClick(getCurrentPagePath());
                return;
            }

            trackEvent("contact", { method });
            trackContactClick(method, getCurrentPagePath());
        }

        document.addEventListener("click", handleClick);

        return () => document.removeEventListener("click", handleClick);
    }, []);

    return null;
}
