import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getBackendContent, resolveBackendAsset } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Trust() {
    const [content, { locale, translations }] = await Promise.all([
        getBackendContent(),
        getSiteSettings(),
    ]);
    const partners = (content.partners ?? []).filter((partner) => partner.name);

    if (!partners.length) return null;

    const title = translateText(translations, "home.trust.title", locale, null);

    return (
        <section className="border-y border-outline-variant/10 bg-surface-container-lowest/50 py-unit-xl">
            <div className="mx-auto max-w-container-max px-margin-desktop">
                {title ? (
                    <Typography
                        as="h2"
                        className="mb-unit-lg text-center"
                        variant="footer-title"
                    >
                        {title}
                    </Typography>
                ) : null}

                <div className="flex flex-wrap items-center justify-center gap-unit-xl opacity-70 grayscale transition-all duration-700 hover:grayscale-0">
                    {partners.map((partner) =>
                        partner.logo ? (
                            <Image
                                alt={partner.name}
                                className="h-12 w-auto object-contain"
                                height={48}
                                key={partner.name}
                                src={resolveBackendAsset(partner.logo, "")}
                                unoptimized
                                width={160}
                            />
                        ) : (
                            <div
                                className="font-headline-md text-headline-md font-bold"
                                key={partner.name}
                            >
                                {partner.name}
                            </div>
                        ),
                    )}
                </div>
            </div>
        </section>
    );
}
