import Image from "@/components/ui/Image";
import { getBackendContent, resolveBackendAsset } from "@/lib/backend";

export default async function PartnerSection() {
    const content = await getBackendContent();
    const partners = (content.partners ?? []).filter((partner) => partner.name);

    if (!partners.length) return null;

    return (
        <section className="border-y border-outline-variant/10 bg-surface-container-lowest/50 py-unit-lg">
            <div className="mx-auto flex max-w-container-max flex-wrap items-center justify-center gap-unit-lg px-margin-desktop opacity-50 grayscale transition-all duration-500 hover:grayscale-0 md:justify-between">
                {partners.map((partner) =>
                    partner.logo ? (
                        <Image
                            alt={partner.name}
                            className="h-10 w-auto object-contain"
                            height={40}
                            key={partner.name}
                            src={resolveBackendAsset(partner.logo, "")}
                            unoptimized
                            width={140}
                        />
                    ) : (
                        <span
                            className="font-headline-sm text-headline-sm font-bold tracking-tighter"
                            key={partner.name}
                        >
                            {partner.name}
                        </span>
                    ),
                )}
            </div>
        </section>
    );
}
