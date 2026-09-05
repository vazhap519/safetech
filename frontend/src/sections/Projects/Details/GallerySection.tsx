"use client";

import { useCallback, useEffect, useState } from "react";

import Image from "@/components/ui/Image";
import TranslatedText from "@/components/i18n/TranslatedText";
import type { ProjectDetail } from "@/lib/projectDetails";

export default function GallerySection({ project }: { project: ProjectDetail }) {
    const gallery = project.gallery;
    const [activeIndex, setActiveIndex] = useState(0);
    const [lightboxOpen, setLightboxOpen] = useState(false);

    const activeImage = gallery[activeIndex];

    const previousImage = useCallback(() => {
        if (gallery.length < 2) return;
        setActiveIndex((current) =>
            current === 0 ? gallery.length - 1 : current - 1,
        );
    }, [gallery.length]);

    const nextImage = useCallback(() => {
        if (gallery.length < 2) return;
        setActiveIndex((current) => (current + 1) % gallery.length);
    }, [gallery.length]);

    useEffect(() => {
        if (!lightboxOpen) return;

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = "hidden";

        const handleKeyDown = (event: KeyboardEvent) => {
            if (event.key === "Escape") setLightboxOpen(false);
            if (event.key === "ArrowLeft") previousImage();
            if (event.key === "ArrowRight") nextImage();
        };

        window.addEventListener("keydown", handleKeyDown);

        return () => {
            document.body.style.overflow = previousOverflow;
            window.removeEventListener("keydown", handleKeyDown);
        };
    }, [lightboxOpen, nextImage, previousImage]);

    if (!gallery.length) return null;

    return (
        <section
            aria-labelledby="project-gallery-title"
            className="mx-auto max-w-container-max px-4 py-12 sm:px-6 sm:py-16 lg:px-margin-desktop"
            style={{ contentVisibility: "auto", containIntrinsicSize: "900px" }}
        >
            <div className="mb-6 flex flex-col gap-3 sm:mb-8 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p className="text-sm font-semibold uppercase tracking-[0.14em] text-secondary">
                        <TranslatedText
                            fallback={{ ka: "გალერია", en: "Gallery", ru: "Галерея" }}
                            translationKey="projects.detail.gallery.eyebrow"
                        />
                    </p>
                    <h2
                        className="mt-2 text-2xl font-semibold text-on-surface sm:text-3xl"
                        id="project-gallery-title"
                    >
                        <TranslatedText
                            fallback={{
                                ka: "პროექტის ფოტოები",
                                en: "Project photos",
                                ru: "Фотографии проекта",
                            }}
                            translationKey="projects.detail.gallery.title"
                        />
                    </h2>
                </div>
                <span className="inline-flex w-fit items-center rounded-full border border-outline-variant/10 bg-surface-container-low px-3 py-1.5 text-sm text-on-surface-variant">
                    {gallery.length} <span className="ml-1">ფოტო</span>
                </span>
            </div>

            <div className="grid gap-4 lg:grid-cols-[minmax(0,1fr)_260px] lg:gap-5">
                <div className="group relative min-h-[300px] overflow-hidden rounded-[26px] border border-outline-variant/10 bg-surface-container-low shadow-[0_20px_60px_rgba(0,0,0,0.18)] sm:min-h-[460px] lg:min-h-[560px]">
                    {activeImage ? (
                        <button
                            aria-label="Open gallery image"
                            className="absolute inset-0 h-full w-full cursor-zoom-in text-left"
                            onClick={() => setLightboxOpen(true)}
                            type="button"
                        >
                            <Image
                                key={activeImage.src}
                                alt={activeImage.alt}
                                className="object-cover transition duration-700 group-hover:scale-[1.02]"
                                fill
                                priority={false}
                                sizes="(max-width: 1024px) 100vw, 75vw"
                                src={activeImage.src}
                            />
                            <span className="absolute inset-0 bg-gradient-to-t from-black/55 via-black/5 to-transparent" />
                            <span className="absolute bottom-4 left-4 right-4 flex items-end justify-between gap-3 sm:bottom-5 sm:left-5 sm:right-5">
                                <span className="max-w-[75%] text-sm leading-6 text-white/90 sm:text-base">
                                    {activeImage.alt}
                                </span>
                                <span className="rounded-full border border-white/15 bg-black/45 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-md sm:text-sm">
                                    {activeIndex + 1} / {gallery.length}
                                </span>
                            </span>
                        </button>
                    ) : null}

                    {gallery.length > 1 ? (
                        <>
                            <button
                                aria-label="Previous gallery image"
                                className="absolute left-3 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/10 bg-black/45 text-2xl text-white opacity-90 backdrop-blur-md transition hover:scale-105 hover:bg-black/70 sm:left-4"
                                onClick={previousImage}
                                type="button"
                            >
                                ‹
                            </button>
                            <button
                                aria-label="Next gallery image"
                                className="absolute right-3 top-1/2 z-10 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/10 bg-black/45 text-2xl text-white opacity-90 backdrop-blur-md transition hover:scale-105 hover:bg-black/70 sm:right-4"
                                onClick={nextImage}
                                type="button"
                            >
                                ›
                            </button>
                        </>
                    ) : null}
                </div>

                {gallery.length > 1 ? (
                    <div className="grid grid-cols-3 gap-3 lg:max-h-[560px] lg:grid-cols-1 lg:overflow-y-auto lg:pr-1">
                        {gallery.map((image, index) => (
                            <button
                                aria-label={`Show gallery image ${index + 1}`}
                                aria-pressed={index === activeIndex}
                                className={`group/thumb relative aspect-[4/3] overflow-hidden rounded-2xl border transition duration-300 ${
                                    index === activeIndex
                                        ? "border-secondary ring-2 ring-secondary/25"
                                        : "border-outline-variant/10 opacity-70 hover:border-outline-variant/25 hover:opacity-100"
                                }`}
                                key={`${image.src}-${index}`}
                                onClick={() => setActiveIndex(index)}
                                type="button"
                            >
                                <Image
                                    alt=""
                                    className="object-cover transition duration-500 group-hover/thumb:scale-105"
                                    fill
                                    sizes="(max-width: 1024px) 33vw, 260px"
                                    src={image.src}
                                />
                                <span className="absolute inset-0 bg-black/0 transition group-hover/thumb:bg-black/10" />
                                <span className="absolute bottom-2 right-2 rounded-full bg-black/55 px-2 py-1 text-[11px] font-semibold text-white backdrop-blur-sm">
                                    {index + 1}
                                </span>
                            </button>
                        ))}
                    </div>
                ) : null}
            </div>

            {lightboxOpen && activeImage ? (
                <div
                    aria-label="Project gallery preview"
                    aria-modal="true"
                    className="fixed inset-0 z-[120] flex items-center justify-center bg-black/95 p-3 sm:p-6"
                    onClick={() => setLightboxOpen(false)}
                    role="dialog"
                >
                    <button
                        aria-label="Close gallery preview"
                        className="absolute right-4 top-4 z-20 grid h-11 w-11 place-items-center rounded-full border border-white/10 bg-white/10 text-2xl text-white backdrop-blur-md transition hover:bg-white/20 sm:right-6 sm:top-6"
                        onClick={() => setLightboxOpen(false)}
                        type="button"
                    >
                        ×
                    </button>

                    {gallery.length > 1 ? (
                        <>
                            <button
                                aria-label="Previous gallery image"
                                className="absolute left-2 top-1/2 z-20 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/10 bg-white/10 text-3xl text-white backdrop-blur-md transition hover:bg-white/20 sm:left-6"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    previousImage();
                                }}
                                type="button"
                            >
                                ‹
                            </button>
                            <button
                                aria-label="Next gallery image"
                                className="absolute right-2 top-1/2 z-20 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/10 bg-white/10 text-3xl text-white backdrop-blur-md transition hover:bg-white/20 sm:right-6"
                                onClick={(event) => {
                                    event.stopPropagation();
                                    nextImage();
                                }}
                                type="button"
                            >
                                ›
                            </button>
                        </>
                    ) : null}

                    <div
                        className="relative h-[80vh] w-[94vw] max-w-7xl"
                        onClick={(event) => event.stopPropagation()}
                    >
                        <Image
                            alt={activeImage.alt}
                            className="object-contain"
                            fill
                            sizes="94vw"
                            src={activeImage.src}
                        />
                    </div>

                    <div className="absolute bottom-4 left-1/2 flex max-w-[85vw] -translate-x-1/2 items-center gap-3 rounded-full border border-white/10 bg-black/45 px-4 py-2 text-sm text-white backdrop-blur-md sm:bottom-6">
                        <span className="truncate">{activeImage.alt}</span>
                        <span className="shrink-0 text-white/60">
                            {activeIndex + 1} / {gallery.length}
                        </span>
                    </div>
                </div>
            ) : null}
        </section>
    );
}
