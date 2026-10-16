"use client";

import Link from "next/link";
import MarketingPixels from "@/components/analytics/MarketingPixels";
import ConsentFooterCode from "@/components/analytics/ConsentFooterCode";
import ConsentSettingsButton from "@/components/analytics/ConsentSettingsButton";
import { useLeadForm } from "@/hooks/useLeadForm";

// Copied into a temporary app route by CI, never shipped as a production route.
export default function MarketingHarness({ mode, step }: { mode: string; step: string }) {
    const form = useLeadForm("qa-marketing");
    return <main data-marketing-harness className="px-5 pt-40">
        <MarketingPixels enabled initialConsent="unknown"
            googleAnalyticsId={mode === "both" ? "G-QATEST1" : undefined}
            googleTagManagerId={mode === "both" || mode === "gtm" ? "GTM-QATEST2" : undefined}
            metaPixelId="123456789" />
        <ConsentFooterCode enabled code={'<div data-qa-counter></div><script>window.qaFooterLoads=(window.qaFooterLoads||0)+1</script>'} />
        <ConsentFooterCode enabled code={'<div id="top-ge-counter-container" data-site-id="12345"></div><script async src="https://counter.top.ge/counter.js"></script>'} />
        <ConsentSettingsButton enabled />
        <Link href={`/qa-marketing/${step === "one" ? "two" : "one"}?mode=${mode}`}>Next test view</Link>
        <a href="tel:+995555000111" onClick={(event) => event.preventDefault()}>QA phone</a>
        <a href="mailto:qa@safetech.test" onClick={(event) => event.preventDefault()}>QA email</a>
        <a href="https://wa.me/995555000111" onClick={(event) => event.preventDefault()}>QA WhatsApp</a>
        <form data-qa-lead onSubmit={form.submit}>
            <input name="firstName" aria-label="QA name" defaultValue="Test" />
            <input name="lastName" defaultValue="Customer" />
            <input name="phone" defaultValue="+995555000111" />
            <input name="email" defaultValue="qa@safetech.test" />
            <input name="address" defaultValue="Tbilisi" />
            <input name="message" defaultValue="Test enquiry" />
            <input name="serviceSlug" defaultValue="security-camera-installation" />
            <input name="privacy" type="hidden" value="1" />
            <button type="submit">Submit test enquiry</button>
            <p role="status">{form.message}</p>
        </form>
    </main>;
}
