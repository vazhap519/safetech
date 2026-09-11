"use client";

import SocialIcon from "@/components/ui/SocialIcon";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import { buildWhatsAppUrl } from "@/lib/whatsapp";

const DEFAULT_MESSAGE =
    "გამარჯობა, მაინტერესებს თქვენი მომსახურება.";

type FloatingWhatsAppProps = {
    callPhone?: string;
    message?: string;
    whatsappPhone?: string;
};

export default function FloatingWhatsApp({
    callPhone,
    message = DEFAULT_MESSAGE,
    whatsappPhone,
}: FloatingWhatsAppProps) {
    const { t } = useLocalization();
    const href = buildWhatsAppUrl(whatsappPhone, message);
    const callHref = callPhone
        ? `tel:${callPhone.replace(/[^\d+]/g, "")}`
        : "";

    if (!href && !callHref) return null;

    const ariaLabel = t("floating.whatsapp.aria", {
        ka: "WhatsApp-ით დაკავშირება",
        en: "Contact us on WhatsApp",
        ru: "Связаться через WhatsApp",
    });
    const tooltip = t("floating.whatsapp.tooltip", {
        ka: "მოგვწერეთ WhatsApp-ზე",
        en: "Message us on WhatsApp",
        ru: "Напишите нам в WhatsApp",
    });
    const callLabel = t("floating.call.aria", {
        ka: "დარეკვა",
        en: "Call now",
        ru: "Позвонить",
    });

    return (
        <>
            <nav
                aria-label={t("floating.contact.aria", {
                    ka: "სწრაფი დაკავშირება",
                    en: "Quick contact",
                    ru: "Быстрая связь",
                })}
                className="fixed inset-x-3 z-50 grid gap-2 rounded-2xl border border-outline-variant/30 bg-surface-container-high/95 p-2 shadow-2xl backdrop-blur sm:hidden"
                style={{
                    bottom: "max(0.75rem, env(safe-area-inset-bottom))",
                    gridTemplateColumns: `repeat(${Number(Boolean(callHref)) + Number(Boolean(href))}, minmax(0, 1fr))`,
                }}
            >
                {callHref ? (
                    <a
                        className="inline-flex min-h-12 items-center justify-center rounded-xl bg-primary-container px-4 font-semibold text-on-primary-container"
                        href={callHref}
                    >
                        {callLabel}
                    </a>
                ) : null}
                {href ? (
                    <a
                        className="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 font-semibold text-white"
                        href={href}
                        rel="noopener noreferrer"
                        target="_blank"
                    >
                        <SocialIcon className="size-5" network="whatsapp" />
                        WhatsApp
                    </a>
                ) : null}
            </nav>

            {href ? (
                <a
                    aria-label={ariaLabel}
                    className="group fixed right-7 z-50 isolate hidden size-16 place-items-center rounded-full bg-[#25D366] text-white shadow-[0_12px_35px_rgba(37,211,102,.4)] transition-transform hover:scale-110 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-[#25D366]/40 sm:grid"
                    href={href}
                    rel="noopener noreferrer"
                    style={{ bottom: "max(1.25rem, env(safe-area-inset-bottom))" }}
                    target="_blank"
                    title="WhatsApp"
                >
                    <span
                        aria-hidden="true"
                        className="absolute inset-0 -z-10 rounded-full bg-[#25D366]/50 motion-safe:animate-ping"
                    />
                    <span className="pointer-events-none absolute right-full mr-3 whitespace-nowrap rounded-lg bg-surface-container-high px-3 py-2 text-sm font-medium text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100 group-focus-visible:opacity-100">
                        {tooltip}
                    </span>
                    <SocialIcon className="size-8" network="whatsapp" />
                </a>
            ) : null}
        </>
    );
}
