"use client";

import { useMemo, useState } from "react";

import Image from "@/components/ui/Image";

type GalleryImage = {
    src: string;
    thumb?: string;
    alt: string;
};

export default function ProductGallery({
    images,
    name,
}: {
    images: GalleryImage[];
    name: string;
}) {
    const [activeIndex, setActiveIndex] = useState(0);
    const slides = useMemo(
        () => images.filter((image) => image.src),
        [images],
    );

    if (!slides.length) {
        return (
            <div className="grid aspect-[4/3] place-items-center rounded-[32px] border border-outline-variant/15 bg-surface-container-low text-on-surface-variant">
                {name}
            </div>
        );
    }

    const activeImage = slides[Math.min(activeIndex, slides.length - 1)];

    return (
        <div className="space-y-4">
            <div className="relative aspect-[4/3] overflow-hidden rounded-[32px] border border-outline-variant/15 bg-surface-container-low shadow-2xl shadow-black/10">
                <Image
                    alt={activeImage.alt || name}
                    className="transition-transform duration-500"
                    fill
                    priority={activeIndex === 0}
                    sizes="(max-width: 1280px) 100vw, 55vw"
                    src={activeImage.src}
                />
            </div>

            {slides.length > 1 ? (
                <div className="no-scrollbar flex gap-3 overflow-x-auto pb-1">
                    {slides.map((image, index) => {
                        const active = index === activeIndex;

                        return (
                            <button
                                aria-label={`${name} image ${index + 1}`}
                                aria-pressed={active}
                                className={`relative h-20 w-24 shrink-0 overflow-hidden rounded-2xl border transition-all sm:h-24 sm:w-28 ${
                                    active
                                        ? "border-primary shadow-lg shadow-primary/20"
                                        : "border-outline-variant/20 opacity-80 hover:opacity-100"
                                }`}
                                key={`${image.src}-${index}`}
                                onClick={() => setActiveIndex(index)}
                                type="button"
                            >
                                <Image
                                    alt={image.alt || name}
                                    fill
                                    sizes="112px"
                                    src={image.thumb || image.src}
                                />
                            </button>
                        );
                    })}
                </div>
            ) : null}
        </div>
    );
}
