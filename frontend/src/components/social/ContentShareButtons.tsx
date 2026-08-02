"use client";

import { useRef, useState } from "react";

import SocialIcon, {
    type SocialIconName,
} from "@/components/ui/SocialIcon";
import type { Locale } from "@/lib/locales";
import type { SiteShareButton } from "@/lib/site-settings";

type ContentShareButtonsProps = {
    heading: string;
    locale: Locale;
    pageTitle: string;
    buttons: SiteShareButton[];
};

function defaultLabel(type: SiteShareButton["type"], locale: Locale) {
    if (type === "copy") {
        return locale === "ka"
            ? "ბმულის კოპირება"
            : locale === "ru"
              ? "Копировать ссылку"
              : "Copy link";
    }

    if (type === "native") {
        return locale === "ka"
            ? "გაზიარება"
            : locale === "ru"
              ? "Поделиться"
              : "Share";
    }

    return {
        facebook: "Facebook",
        whatsapp: "WhatsApp",
        telegram: "Telegram",
        linkedin: "LinkedIn",
        x: "X",
        pinterest: "Pinterest",
        viber: "Viber",
        email: "Email",
    }[type];
}

function statusText(locale: Locale) {
    return locale === "ka"
        ? "ბმული დაკოპირდა"
        : locale === "ru"
          ? "Ссылка скопирована"
          : "Link copied";
}

function iconName(type: SiteShareButton["type"]): SocialIconName {
    if (type === "native") return "share";
    if (type === "copy") return "copy";
    return type;
}

function currentPageUrl() {
    return `${window.location.origin}${window.location.pathname}`;
}

function externalShareUrl(
    type: Exclude<SiteShareButton["type"], "native" | "copy">,
    pageTitle: string,
    pageUrl: string,
) {
    const title = encodeURIComponent(pageTitle);
    const url = encodeURIComponent(pageUrl);

    switch (type) {
        case "facebook":
            return `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        case "whatsapp":
            return `https://wa.me/?text=${title}%20${url}`;
        case "telegram":
            return `https://t.me/share/url?url=${url}&text=${title}`;
        case "linkedin":
            return `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
        case "x":
            return `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
        case "pinterest":
            return `https://pinterest.com/pin/create/button/?url=${url}&description=${title}`;
        case "viber":
            return `viber://forward?text=${title}%20${url}`;
        case "email":
            return `mailto:?subject=${title}&body=${title}%0A${url}`;
    }
}

async function copyToClipboard(value: string) {
    if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(value);
        return;
    }

    const textarea = document.createElement("textarea");
    textarea.value = value;
    textarea.setAttribute("readonly", "");
    textarea.style.position = "fixed";
    textarea.style.opacity = "0";
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand("copy");
    textarea.remove();
}

export default function ContentShareButtons({
    heading,
    locale,
    pageTitle,
    buttons,
}: ContentShareButtonsProps) {
    const [message, setMessage] = useState("");
    const resetTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    if (!buttons.length) return null;

    async function handleShare(button: SiteShareButton) {
        const pageUrl = currentPageUrl();

        if (button.type === "copy") {
            await copyToClipboard(pageUrl);
            setMessage(statusText(locale));

            if (resetTimer.current) clearTimeout(resetTimer.current);
            resetTimer.current = setTimeout(() => setMessage(""), 2500);
            return;
        }

        if (button.type === "native") {
            if (navigator.share) {
                try {
                    await navigator.share({ title: pageTitle, url: pageUrl });
                    return;
                } catch (error) {
                    if (error instanceof DOMException && error.name === "AbortError") {
                        return;
                    }
                }
            }

            await copyToClipboard(pageUrl);
            setMessage(statusText(locale));
            return;
        }

        const shareUrl = externalShareUrl(button.type, pageTitle, pageUrl);

        if (shareUrl.startsWith("mailto:") || shareUrl.startsWith("viber:")) {
            window.location.href = shareUrl;
            return;
        }

        window.open(
            shareUrl,
            "safetech-share",
            "noopener,noreferrer,width=720,height=640",
        );
    }

    return (
        <section
            aria-labelledby="content-share-heading"
            className="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8"
        >
            <div className="flex flex-col gap-5 rounded-3xl border border-white/10 bg-surface-container/70 p-5 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:p-6">
                <h2
                    className="font-headline-md text-xl font-semibold text-on-surface"
                    id="content-share-heading"
                >
                    {heading}
                </h2>
                <div className="flex flex-wrap items-center gap-3">
                    {buttons.map((button) => {
                        const label = button.label || defaultLabel(button.type, locale);

                        return (
                            <button
                                aria-label={label}
                                className="group inline-flex min-h-11 items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-on-surface-variant transition hover:-translate-y-0.5 hover:border-secondary/50 hover:bg-secondary/10 hover:text-secondary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                                key={button.type}
                                onClick={() => void handleShare(button)}
                                title={label}
                                type="button"
                            >
                                <SocialIcon
                                    className="size-5 transition-transform group-hover:scale-110"
                                    network={iconName(button.type)}
                                />
                                <span>{label}</span>
                            </button>
                        );
                    })}
                </div>
                <p aria-live="polite" className="sr-only" role="status">
                    {message}
                </p>
            </div>
        </section>
    );
}
