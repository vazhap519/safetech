"use client";

import { useEffect } from "react";
import { usePathname } from "next/navigation";

import { stripLocalePrefix } from "@/lib/locales";

export default function RouteScrollManager() {
    const pathname = usePathname();

    useEffect(() => {
        const storageKey = "safetech:last-pathname";
        const previousPath = window.sessionStorage.getItem(storageKey);
        let frame: number | null = null;
        let timeout: number | null = null;

        if ("scrollRestoration" in window.history) {
            window.history.scrollRestoration = "manual";
        }

        window.sessionStorage.setItem(storageKey, pathname);

        if (
            previousPath &&
            stripLocalePrefix(previousPath) !== stripLocalePrefix(pathname)
        ) {
            window.scrollTo({ top: 0, left: 0, behavior: "auto" });
            frame = window.requestAnimationFrame(() => {
                window.scrollTo({ top: 0, left: 0, behavior: "auto" });
            });
            timeout = window.setTimeout(() => {
                window.scrollTo({ top: 0, left: 0, behavior: "auto" });
            }, 0);
        }

        return () => {
            if (frame !== null) window.cancelAnimationFrame(frame);
            if (timeout !== null) window.clearTimeout(timeout);
        };
    }, [pathname]);

    return null;
}
