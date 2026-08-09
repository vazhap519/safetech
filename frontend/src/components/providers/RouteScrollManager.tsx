"use client";

import { useEffect } from "react";
import { usePathname } from "next/navigation";

import { stripLocalePrefix } from "@/lib/locales";

export default function RouteScrollManager() {
    const pathname = usePathname();

    useEffect(() => {
        const storageKey = "safetech:last-pathname";
        let previousPath: string | null = null;
        let frame: number | null = null;
        let timeout: number | null = null;

        // Some privacy/private browser modes can expose sessionStorage but throw
        // when it is accessed. Route scroll management must never take down the app.
        try {
            previousPath = window.sessionStorage.getItem(storageKey);
            window.sessionStorage.setItem(storageKey, pathname);
        } catch {
            previousPath = null;
        }

        try {
            if ("scrollRestoration" in window.history) {
                window.history.scrollRestoration = "manual";
            }
        } catch {
            // Scroll restoration is optional; ignore browsers that restrict it.
        }

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
