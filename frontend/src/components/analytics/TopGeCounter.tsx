"use client";

import { useEffect, useRef } from "react";
import { usePathname } from "next/navigation";
import { hasAnalyticsConsent } from "@/lib/consent";

// Use TOP.GE's public image endpoint without its permanent History API patch.
// React owns navigation and removal, so a revoked counter cannot keep tracking.
export default function TopGeCounter({ siteId }: { siteId: string }) {
    const pathname = usePathname();
    const imageRef = useRef<HTMLImageElement>(null);
    const previousUrlRef = useRef<string | null>(null);

    useEffect(() => {
        if (!hasAnalyticsConsent() || !imageRef.current) return;
        const location = window.location.href.slice(0, 1000);
        const fields = [
            ["ID", siteId], ["JS", "11"], ["RAND", String(Math.random())],
            ["ISFRM", window.self === window.top ? "0" : "1"],
            ["REFERER", (previousUrlRef.current ?? document.referrer).slice(0, 1000)],
            ["RESOLUTION", `${screen.width}x${screen.height}`],
            ["JL", location], ["DEPT", String(screen.colorDepth)],
        ];
        imageRef.current.src = `https://counter.top.ge/cgi-bin/count222?${fields
            .map(([key, value]) => `${key}:${encodeURIComponent(value)}`).join("+")}`;
        previousUrlRef.current = location;
    }, [pathname, siteId]);

    return <a href={`https://www.top.ge/index.php?h=${siteId}#${siteId}`} target="_blank" rel="noopener noreferrer">
        {/* The counter is a dynamic third-party image, not an optimizable asset. */}
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img ref={imageRef} alt="TOP.GE" title="TOP.GE" />
    </a>;
}
