"use client";

import { useEffect, useRef, useSyncExternalStore } from "react";
import { readAnalyticsConsent, subscribeToAnalyticsConsent } from "@/lib/consent";

// This is trusted CMS code, subject to the site's existing CSP. Load it only
// after consent; React's innerHTML alone would not execute scripts on navigation.
export default function ConsentFooterCode({ code, enabled }: { code: string; enabled: boolean }) {
    const consent = useSyncExternalStore(subscribeToAnalyticsConsent, readAnalyticsConsent, () => "unknown");
    const container = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const node = container.current;
        if (!node) return;
        node.replaceChildren();
        if (!enabled || consent !== "accepted" || !code) return;

        const template = document.createElement("template");
        template.innerHTML = code;
        node.append(template.content.cloneNode(true));
        for (const inert of node.querySelectorAll("script")) {
            const script = document.createElement("script");
            for (const attribute of inert.attributes) script.setAttribute(attribute.name, attribute.value);
            script.textContent = inert.textContent;
            inert.replaceWith(script);
        }
        return () => node.replaceChildren();
    }, [code, consent, enabled]);

    return <div className="flex min-h-5 items-center justify-center" ref={container} />;
}
