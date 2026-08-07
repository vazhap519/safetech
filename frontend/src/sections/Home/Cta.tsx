"use client";

import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import { useLocalization } from "@/components/providers/LocalizationProvider";
import Typography from "@/components/ui/Typography";

export default function Cta() {
    const { t } = useLocalization();
    const eyebrow = t("home.cta.eyebrow", null);
    const title = t("home.cta.title", null);
    const description = t("home.cta.description", null);
    const ctaLabel = t("home.cta.submit", {
        ka: "კონსულტაციის მიღება",
        en: "Get consultation",
        ru: "Получить консультацию",
    });
    const note = t("home.cta.note", null);

    if (!eyebrow && !title && !description && !ctaLabel && !note) {
        return null;
    }

    return (
        <section className="relative mx-auto max-w-container-max overflow-hidden px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14">
            <div className="mesh-gradient relative z-10 overflow-hidden rounded-2xl border border-outline-variant/20 px-6 py-12 text-center sm:px-10 md:px-14 md:py-16">
                <div className="absolute inset-0 bg-primary/5 opacity-70 blur-[120px]" />

                <div className="relative z-20 mx-auto max-w-4xl space-y-6">
                    {eyebrow ? (
                        <span className="inline-block font-mono-sm text-mono-sm font-semibold uppercase text-primary">
                            {eyebrow}
                        </span>
                    ) : null}

                    {title ? (
                        <Typography as="h2" variant="cta-title">
                            {title}
                        </Typography>
                    ) : null}

                    {description ? (
                        <Typography
                            as="p"
                            className="mx-auto max-w-2xl"
                            variant="section-description"
                        >
                            {description}
                        </Typography>
                    ) : null}

                    {ctaLabel ? (
                        <div className="flex justify-center pt-2">
                            <ConsultationTrigger className="inline-flex min-h-12 items-center justify-center rounded-xl bg-primary-container px-7 py-3.5 font-semibold text-on-primary-container shadow-lg shadow-blue-500/20 transition hover:brightness-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-surface">
                                {ctaLabel}
                            </ConsultationTrigger>
                        </div>
                    ) : null}

                    {note ? (
                        <p className="text-sm font-label-md leading-relaxed text-on-surface-variant/60 md:text-label-md">
                            {note}
                        </p>
                    ) : null}
                </div>
            </div>
        </section>
    );
}
