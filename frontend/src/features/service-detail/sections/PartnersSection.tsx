import Image from "@/components/ui/Image";
import { getBackendContent, resolveBackendAsset } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function PartnersSection() {
    const [{ locale, translations }, content] = await Promise.all([
        getSiteSettings(),
        getBackendContent(),
    ]);
    const ariaLabel = translateText(
        translations,
        "service.detail.partners.ariaLabel",
        locale,
        null,
    );
    const partners = (content.partners ?? []).filter(
        (partner) => partner.name || partner.logo,
    );

    if (!partners.length) return null;

    return (
        <section
            aria-label={ariaLabel || undefined}
            className="border-y border-white/5 bg-background px-margin-desktop py-unit-lg"
        >
            <ul className="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                {partners.map((partner) => (
                    <li
                        className="rounded-full border border-white/8 bg-white/[0.03] px-4 py-2 font-headline-md text-base text-on-surface-variant transition-all duration-300 hover:-translate-y-0.5 hover:border-secondary/30 hover:bg-secondary/10 hover:text-on-surface"
                        key={`${partner.name}-${partner.logo}`}
                    >
                        {partner.logo ? (
                            <Image
                                alt={partner.name}
                                className="h-8 w-auto object-contain"
                                height={32}
                                src={resolveBackendAsset(partner.logo, "")}
                                unoptimized
                                width={120}
                            />
                        ) : (
                            partner.name
                        )}
                    </li>
                ))}
            </ul>
        </section>
    );
}
