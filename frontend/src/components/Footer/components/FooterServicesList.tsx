"use client";

import TranslatedText from "@/components/i18n/TranslatedText";
import LocalizedLink from "@/components/ui/LocalizedLink";
import type { FooterServiceLink } from "@/lib/backend";

export default function FooterServicesList({
    services,
}: {
    services: FooterServiceLink[];
}) {
    return (
        <ul className="space-y-1 text-[15px] leading-7 text-on-surface-variant">
            {services.map((service) => (
                <li key={service.slug}>
                    <LocalizedLink
                        className="inline-flex min-h-9 items-center transition-colors duration-300 hover:text-secondary"
                        href={`/services/${service.slug}`}
                        prefetch={false}
                    >
                        <TranslatedText
                            fallback={service.titles}
                            translationKey={`service.${service.slug}.card.title`}
                        />
                    </LocalizedLink>
                </li>
            ))}
        </ul>
    );
}
