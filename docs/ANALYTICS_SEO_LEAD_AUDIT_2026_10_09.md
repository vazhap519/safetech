# SafeTech GA4, SEO & Lead Quality Audit — 2026-10-09

Status: **draft / not deployed**. Branch: `fix/ga4-conversion-tracking`.

## Verified code fixes in this branch
- GA4 `page_view` emitted for the first consented page and subsequent client-side route changes using one direct GA4 path.
- Suppress the redundant GTM loader when direct GA4 is configured (published GTM container was empty at time of review).
- Keep automatic GA4 `send_page_view` disabled in the consent-time config.
- Emit separate consented GA4 `phone_click`, `whatsapp_click`, and `email_click` alongside existing internal metrics.

## Confirmed from code, not yet live-browser verified
- `useLeadForm` sends `generate_lead` only after a successful HTTP response from `/api/contact-leads`. The camera planner and AI assistant have additional generation paths requiring individual tests.
- The consent flow has distinct accepted / rejected / unknown states. Verify no events before consent and no duplicate page view on accept.
- Events that represent a telephone or messaging click are *intent signals*, not completed calls or confirmed sales.
- `form_start` is not explicitly emitted in the frontend's code search. GA4 Enhanced Measurement forms handling is a candidate explanation for the anomalously high count; use DebugView and inspect triggering DOM interactions before changing any reporting configuration.

## SEO observations and follow-up
- Home page exposes visible service navigation and location/service links.
- The Georgian home page currently renders a Russian specification label (`Сетевая технология`) in the Lisi project card. This appears to be CMS content and should be corrected in the Filament project translation fields, not blanket-translated in frontend code.
- Verify per-language Project/Service titles, descriptions, OG images, canonical/hreflang, sitemap inclusion, published/noindex and 301 legacy redirects against production, prioritizing indexable Georgia-facing commercial pages.
- The live sitemap.xml and robots.txt could not be verified using the available web fetch; this is **not** evidence they are broken. Test with curl/browser and Search Console.
- No organic rankings, conversion uplift or lead volume can be guaranteed from technical SEO changes alone.

## Required production validation before merge
1. Run GA4 Tag Assistant / DebugView for first consented landing, internal navigation, refresh and rejected consent; assert exactly one page_view for each valid navigation.
2. Verify `form_start` starts on actual form interaction (not hydration/autofill) and identify traffic sources / bot signals.
3. Submit one test contact lead and confirm one `generate_lead` with non-PII `form_source`. Repeat for camera planner and AI assistant.
4. Click phone, email and WhatsApp links; verify appropriate GA4 events and no duplication from GTM.
5. Verify Google Ads receives selected validated GA4 key events; use primary for confirmed submitted leads, secondary for click proxies, and avoid importing the same action twice.
6. Validate source/medium and `gclid`/UTM attribution, Google Ads auto-tagging, GA4 Ads link, and consent handling.
7. Check Search Console indexing, self-canonicals, hreflang, Local SEO service landing pages, current project location and real project proof, contact CTA reachability.
8. Re-run lint, typecheck, build, SEO smoke tests and ensure draft PR checks green.

## Safety
No merge, deploy, GTM publish, or Google Ads conversion modification without separate review/approval.
