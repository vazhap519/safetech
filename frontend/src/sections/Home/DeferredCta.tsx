"use client";

import dynamic from "next/dynamic";
import { useEffect, useRef, useState } from "react";

const HomeCta = dynamic(() => import("@/sections/Home/Cta"), {
    loading: () => (
        <div
            aria-hidden="true"
            className="mx-auto min-h-[32rem] max-w-container-max px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14"
        />
    ),
    ssr: false,
});

export default function DeferredCta() {
    const containerRef = useRef<HTMLDivElement>(null);
    const [shouldLoad, setShouldLoad] = useState(false);

    useEffect(() => {
        const element = containerRef.current;

        if (!element || !("IntersectionObserver" in window)) {
            setShouldLoad(true);
            return;
        }

        const observer = new IntersectionObserver(
            (entries) => {
                if (entries.some((entry) => entry.isIntersecting)) {
                    setShouldLoad(true);
                    observer.disconnect();
                }
            },
            { rootMargin: "800px 0px" },
        );

        observer.observe(element);

        return () => observer.disconnect();
    }, []);

    return (
        <div ref={containerRef}>
            {shouldLoad ? (
                <HomeCta />
            ) : (
                <div
                    aria-hidden="true"
                    className="mx-auto min-h-[32rem] max-w-container-max px-5 py-16 md:px-8 md:py-20 lg:py-24 xl:px-14"
                />
            )}
        </div>
    );
}
