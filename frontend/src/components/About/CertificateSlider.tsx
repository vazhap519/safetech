"use client";

import { useCallback, useEffect, useRef, useState } from "react";

import Image from "@/components/ui/Image";
import type { TeamMemberCertificate } from "@/lib/team";

export type CertificateSliderLabels = {
    eyebrow: string;
    title: string;
    description: string;
    certificate: string;
    previous: string;
    next: string;
    open: string;
    close: string;
    dialog: string;
};

type CertificateSliderProps = {
    certificates: TeamMemberCertificate[];
    memberName: string;
    labels: CertificateSliderLabels;
};

export default function CertificateSlider({
    certificates,
    memberName,
    labels,
}: CertificateSliderProps) {
    const [activeIndex, setActiveIndex] = useState(0);
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const touchStartX = useRef<number | null>(null);
    const closeButtonRef = useRef<HTMLButtonElement>(null);
    const openerRef = useRef<HTMLButtonElement>(null);
    const activeCertificate = certificates[activeIndex];
    const hasMultiple = certificates.length > 1;

    const previous = useCallback(() => {
        if (!hasMultiple) return;
        setActiveIndex((index) =>
            index === 0 ? certificates.length - 1 : index - 1,
        );
    }, [certificates.length, hasMultiple]);

    const next = useCallback(() => {
        if (!hasMultiple) return;
        setActiveIndex((index) => (index + 1) % certificates.length);
    }, [certificates.length, hasMultiple]);

    const openLightbox = (button: HTMLButtonElement) => {
        openerRef.current = button;
        setLightboxOpen(true);
    };

    useEffect(() => {
        if (!lightboxOpen) return;

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";
        closeButtonRef.current?.focus();

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === "Escape") setLightboxOpen(false);
            if (event.key === "ArrowLeft") previous();
            if (event.key === "ArrowRight") next();
        };

        window.addEventListener("keydown", handleKeyDown);

        return () => {
            document.body.style.overflow = previousOverflow;
            window.removeEventListener("keydown", handleKeyDown);
            openerRef.current?.focus();
        };
    }, [lightboxOpen, next, previous]);

    if (!activeCertificate) return null;

    const activeAlt = `${memberName} — ${labels.certificate} ${activeIndex + 1}`;

    return (
        <section
            aria-labelledby="certificate-gallery-title"
            className="bg-surface-container-lowest py-unit-xl"
            style={{ contentVisibility: "auto", containIntrinsicSize: "900px" }}
        >
            <div className="mx-auto max-w-container-max px-4 sm:px-6 lg:px-margin-desktop">
                <header className="mb-8 max-w-3xl">
                    <p className="font-mono-sm text-mono-sm uppercase tracking-[0.2em] text-secondary">
                        {labels.eyebrow}
                    </p>
                    <h2
                        className="mt-3 font-headline-xl text-headline-xl text-white"
                        id="certificate-gallery-title"
                    >
                        {labels.title}
                    </h2>
                    <p className="mt-4 font-body-md text-body-md leading-relaxed text-on-surface-variant">
                        {labels.description}
                    </p>
                </header>

                <div
                    className="grid gap-4 lg:grid-cols-[minmax(0,1fr)_220px]"
                    onKeyDown={(event) => {
                        if (event.key === "ArrowLeft") {
                            event.preventDefault();
                            previous();
                        }
                        if (event.key === "ArrowRight") {
                            event.preventDefault();
                            next();
                        }
                    }}
                    onTouchEnd={(event) => {
                        const startX = touchStartX.current;
                        touchStartX.current = null;
                        if (startX === null) return;

                        const distance = event.changedTouches[0]?.clientX - startX;
                        if (Math.abs(distance) < 45) return;
                        if (distance > 0) previous();
                        else next();
                    }}
                    onTouchStart={(event) => {
                        touchStartX.current = event.touches[0]?.clientX ?? null;
                    }}
                >
                    <div className="group relative min-h-[360px] overflow-hidden rounded-[28px] border border-outline-variant/15 bg-white shadow-[0_24px_80px_rgba(0,0,0,0.28)] sm:min-h-[560px] lg:min-h-[680px]">
                        <button
                            aria-label={`${labels.open}: ${activeAlt}`}
                            className="absolute inset-0 h-full w-full cursor-zoom-in focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-secondary"
                            onClick={(event) => openLightbox(event.currentTarget)}
                            type="button"
                        >
                            <Image
                                key={activeCertificate.src}
                                alt={activeAlt}
                                className="object-contain p-3 sm:p-6"
                                fill
                                sizes="(max-width: 1024px) 100vw, 80vw"
                                src={activeCertificate.src}
                            />
                        </button>

                        <span
                            aria-live="polite"
                            className="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-black/75 px-4 py-2 text-sm font-semibold text-white backdrop-blur-md"
                        >
                            {activeIndex + 1} / {certificates.length}
                        </span>

                        {hasMultiple ? (
                            <>
                                <button
                                    aria-label={labels.previous}
                                    className="absolute left-3 top-1/2 z-10 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-black/65 text-3xl text-white backdrop-blur-md transition hover:bg-black/85 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                                    onClick={previous}
                                    type="button"
                                >
                                    ‹
                                </button>
                                <button
                                    aria-label={labels.next}
                                    className="absolute right-3 top-1/2 z-10 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-black/65 text-3xl text-white backdrop-blur-md transition hover:bg-black/85 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                                    onClick={next}
                                    type="button"
                                >
                                    ›
                                </button>
                            </>
                        ) : null}
                    </div>

                    {hasMultiple ? (
                        <div className="grid grid-cols-3 gap-3 sm:grid-cols-4 lg:max-h-[680px] lg:grid-cols-1 lg:overflow-y-auto lg:pr-1">
                            {certificates.map((certificate, index) => (
                                <button
                                    aria-label={`${labels.certificate} ${index + 1}`}
                                    aria-pressed={index === activeIndex}
                                    className={`relative aspect-[4/3] overflow-hidden rounded-2xl border bg-white transition ${index === activeIndex
                                        ? "border-secondary ring-2 ring-secondary/30"
                                        : "border-outline-variant/15 opacity-70 hover:opacity-100"}`}
                                    key={certificate.id}
                                    onClick={() => setActiveIndex(index)}
                                    type="button"
                                >
                                    <Image
                                        alt=""
                                        className="object-contain p-1"
                                        fill
                                        sizes="(max-width: 1024px) 33vw, 220px"
                                        src={certificate.thumbnail || certificate.src}
                                    />
                                    <span className="absolute bottom-1.5 right-1.5 rounded-full bg-black/70 px-2 py-0.5 text-xs font-semibold text-white">
                                        {index + 1}
                                    </span>
                                </button>
                            ))}
                        </div>
                    ) : null}
                </div>
            </div>

            {lightboxOpen ? (
                <div
                    aria-label={labels.dialog}
                    aria-modal="true"
                    className="fixed inset-0 z-[140] flex items-center justify-center bg-black/95 p-3 sm:p-8"
                    onClick={() => setLightboxOpen(false)}
                    role="dialog"
                >
                    <button
                        aria-label={labels.close}
                        className="absolute right-4 top-4 z-20 grid size-12 place-items-center rounded-full border border-white/15 bg-white/10 text-3xl text-white backdrop-blur-md transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary"
                        onClick={() => setLightboxOpen(false)}
                        ref={closeButtonRef}
                        type="button"
                    >
                        ×
                    </button>

                    {hasMultiple ? (
                        <>
                            <button
                                aria-label={labels.previous}
                                className="absolute left-2 top-1/2 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-3xl text-white backdrop-blur-md transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary sm:left-6"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    previous();
                                }}
                                type="button"
                            >
                                ‹
                            </button>
                            <button
                                aria-label={labels.next}
                                className="absolute right-2 top-1/2 z-20 grid size-12 -translate-y-1/2 place-items-center rounded-full border border-white/15 bg-white/10 text-3xl text-white backdrop-blur-md transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-secondary sm:right-6"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    next();
                                }}
                                type="button"
                            >
                                ›
                            </button>
                        </>
                    ) : null}

                    <div
                        className="relative h-[88vh] w-[92vw] max-w-7xl"
                        onClick={(event) => event.stopPropagation()}
                    >
                        <Image
                            alt={activeAlt}
                            className="object-contain"
                            fill
                            sizes="92vw"
                            src={activeCertificate.src}
                        />
                    </div>
                </div>
            ) : null}
        </section>
    );
}
