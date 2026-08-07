"use client";

import SocialIcon from "@/components/ui/SocialIcon";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import {
    getCurrentPagePath,
    trackWhatsAppClick,
} from "@/lib/analytics-events";
import { buildWhatsAppUrl } from "@/lib/whatsapp";

const DEFAULT_MESSAGE =
    "გამარჯობა, მაინტერესებს თქვენი მომსახურება.";

type FloatingWhatsAppProps = {
    phone?: string;
    message?: string;
};

export default function FloatingWhatsApp({
    phone,
    message = DEFAULT_MESSAGE,
}: FloatingWhatsAppProps) {
    const { t } = useLocalization();
    const href = buildWhatsAppUrl(phone, message);

    if (!href) return null;

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

    return (
        <a
            aria-label={ariaLabel}
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
                {tooltip}
            </span>
            <SocialIcon className="size-7 sm:size-8" network="whatsapp" />
        </a>
    );
}
