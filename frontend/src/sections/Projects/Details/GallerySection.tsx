"use client";

import { useCallback, useEffect, useState } from "react";

import Image from "@/components/ui/Image";
import type { ProjectDetail } from "@/lib/projectDetails";
import { getYouTubeEmbedUrl } from "@/lib/youtube";

const AUTO_ADVANCE_MS = 5000;

export default function GallerySection({
    project,
}: {
    project: ProjectDetail;
}) {
    const embedUrl = getYouTubeEmbedUrl(project.videoUrl);
    const gallery = project.gallery;
    const [activeIndex, setActiveIndex] = useState(0);
    const [videoStarted, setVideoStarted] = useState(false);
    const [lightboxOpen, setLightboxOpen] = useState(false);
    const [paused, setPaused] = useState(false);

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
        if (gallery.length < 2 || paused || lightboxOpen) return;
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

        const timer = window.setInterval(nextImage, AUTO_ADVANCE_MS);
        return () => window.clearInterval(timer);
    }, [gallery.length, lightboxOpen, nextImage, paused]);

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

    if (!embedUrl && !gallery.length) {
        return null;
    }

    const videoPoster = gallery[0] ?? {
        src: project.image,
        alt: project.imageAlt || project.title || project.name,
    };

    return (
        <section
            aria-label={project.title || project.name}
            className="mx-auto max-w-container-max px-4 py-10 sm:px-6 sm:py-unit-xl lg:px-margin-desktop"
            style={{ contentVisibility: "auto", containIntrinsicSize: "900px" }}
        >
            <div
                className={`grid grid-cols-1 gap-4 sm:gap-6 ${
                    embedUrl && gallery.length ? "lg:grid-cols-12" : ""
                }`}
            >
                {embedUrl ? (
                    <div
                        className={`${
                            gallery.length ? "lg:col-span-7" : ""
                        } relative aspect-video w-full overflow-hidden rounded-2xl border border-outline-variant/10 bg-black shadow-xl`}
                    >
                        {videoStarted ? (
                            <iframe
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowFullScreen
                                className="absolute inset-0 h-full w-full"
                                referrerPolicy="strict-origin-when-cross-origin"
                                src={`${embedUrl}&autoplay=1`}
                                title={project.title || project.name}
                            />
                        ) : (
                            <button
                                aria-label="Play project video"
                                className="group absolute inset-0 h-full w-full cursor-pointer"
                                onClick={() => setVideoStarted(true)}
                                type="button"
                            >
                                {videoPoster.src ? (
                                    <Image
                                        alt={videoPoster.alt}
                                        className="object-cover opacity-90 transition duration-300 group-hover:scale-[1.01] group-hover:opacity-100"
                                        fill
                                        sizes={
                                            gallery.length
                                                ? "(max-width: 1024px) 100vw, 58vw"
                                                : "(max-width: 1280px) 100vw, 1200px"
                                        }
                                        src={videoPoster.src}
                                    />
                                ) : null}
                                <span className="absolute inset-0 bg-black/25 transition group-hover:bg-black/15" />
                                <span className="absolute left-1/2 top-1/2 grid h-16 w-16 -translate-x-1/2 -translate-y-1/2 place-items-center rounded-full bg-red-600 text-2xl text-white shadow-2xl transition duration-300 group-hover:scale-105 sm:h-20 sm:w-20 sm:text-3xl">
                                    ▶
                                </span>
                            </button>
                        )}
                    </div>
                ) : null}

                {gallery.length ? (
                    <div
                        className={`${
                            embedUrl ? "lg:col-span-5" : ""
                        } min-w-0`}
                        onMouseEnter={() => setPaused(true)}
                        onMouseLeave={() => setPaused(false)}
                    >
                        <div className="relative aspect-[4/3] overflow-hidden rounded-2xl border border-outline-variant/10 bg-surface-container-low shadow-xl lg:aspect-auto lg:h-full lg:min-h-[360px]">
                            {activeImage ? (
                                <button
                                    aria-label="Open gallery image"
                                    className="absolute inset-0 h-full w-full cursor-zoom-in"
                                    onClick={() => setLightboxOpen(true)}
                                    type="button"
                                >
                                    <Image
                                        key={activeImage.src}
                                        alt={activeImage.alt}
                                        className="object-cover transition-transform duration-500 hover:scale-[1.015]"
                                        fill
                                        sizes={
                                            embedUrl
                                                ? "(max-width: 1024px) 100vw, 42vw"
                                                : "(max-width: 1280px) 100vw, 1200px"
                                        }
                                        src={activeImage.src}
                                    />
                                    <span className="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent" />
                                    <span className="absolute bottom-3 right-3 rounded-full bg-black/55 px-3 py-1.5 text-xs font-medium text-white backdrop-blur-sm sm:text-sm">
                                        {activeIndex + 1} / {gallery.length}
                                    </span>
                                </button>
                            ) : null}

                            {gallery.length > 1 ? (
                                <>
                                    <button
                                        aria-label="Previous gallery image"
                                        className="absolute left-3 top-1/2 z-10 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-black/55 text-xl text-white backdrop-blur-sm transition hover:bg-black/75"
                                        onClick={previousImage}
                                        type="button"
                                    >
                                        ‹
                                    </button>
                                    <button
                                        aria-label="Next gallery image"
                                        className="absolute right-3 top-1/2 z-10 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-full bg-black/55 text-xl text-white backdrop-blur-sm transition hover:bg-black/75"
                                        onClick={nextImage}
                                        type="button"
                                    >
                                        ›
                                    </button>
                                </>
                            ) : null}
                        </div>

                        {gallery.length > 1 ? (
                            <div className="mt-3 flex gap-2 overflow-x-auto pb-1 sm:gap-3">
                                {gallery.map((image, index) => (
                                    <button
                                        aria-label={`Show gallery image ${index + 1}`}
                                        aria-pressed={index === activeIndex}
                                        className={`relative aspect-[4/3] w-24 shrink-0 overflow-hidden rounded-lg border transition sm:w-28 ${
                                            index === activeIndex
                                                ? "border-primary ring-2 ring-primary/30"
                                                : "border-outline-variant/20 opacity-70 hover:opacity-100"
                                        }`}
                                        key={`${image.src}-${index}`}
                                        onClick={() => setActiveIndex(index)}
                                        type="button"
                                    >
                                        <Image
                                            alt=""
                                            className="object-cover"
                                            fill
                                            sizes="112px"
                                            src={image.src}
                                        />
                                    </button>
                                ))}
                            </div>
                        ) : null}
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
                        className="absolute right-4 top-4 z-20 grid h-11 w-11 place-items-center rounded-full bg-white/10 text-2xl text-white backdrop-blur-sm transition hover:bg-white/20 sm:right-6 sm:top-6"
                        onClick={() => setLightboxOpen(false)}
                        type="button"
                    >
                        ×
                    </button>

                    {gallery.length > 1 ? (
                        <>
                            <button
                                aria-label="Previous gallery image"
                                className="absolute left-2 top-1/2 z-20 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-3xl text-white backdrop-blur-sm transition hover:bg-white/20 sm:left-6"
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
                                className="absolute right-2 top-1/2 z-20 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full bg-white/10 text-3xl text-white backdrop-blur-sm transition hover:bg-white/20 sm:right-6"
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
                        className="relative h-[78vh] w-[94vw] max-w-7xl"
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

                    <div className="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-4 py-2 text-sm text-white backdrop-blur-sm sm:bottom-6">
                        {activeIndex + 1} / {gallery.length}
                    </div>
                </div>
            ) : null}
        </section>
    );
}
