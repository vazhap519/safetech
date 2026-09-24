# SafeTech SEO growth — measurement and deployment checklist

**Purpose:** Increase qualified inbound enquiries and completed CCTV / IT jobs, not just impression counts. A third-party post's 3,610 clicks or 87,600 impressions is not SafeTech's actual baseline or a guaranteed outcome.

## 1. Get an actual baseline from Google Search Console

Use verified property `https://safetech.ge/` (or its domain property). Under **Performance → Search results**:

1. Compare the last 3 complete months against the preceding 3 complete months.
2. Export **Queries** and **Pages** with clicks, impressions, CTR and average position; also export Countries and Devices.
3. Restrict service lead evaluation to relevant organic search traffic from Georgia, but keep the unfiltered site total separately for context. Do not assume that every click is a distinct human or interested buyer.
4. Record impressions/clicks for: the main CCTV installation page, existing genuine local pages, camera planner, and any new guides. Keep KA/EN/RU URL variants separate before aggregating.
5. Use **Indexing → Pages** and **URL inspection** to confirm which page versions Google actually indexed. A successful sitemap or CMS `noindex=false` does not prove Google indexing.
6. Never select new city/keyword permutations solely from a keyword list without verifying demand and distinct value for the user.

This baseline requires authenticated Search Console access. Neither GitHub CI nor a third-party visibility estimate can replace it.

## 2. What counts as an enquiry

| Signal | Source | Interpretation |
| --- | --- | --- |
| Search Console clicks | GSC Web Search | Clicks from Google organic search, not unique people or confirmed enquiries. |
| `generate_lead` from consultation | Consent-gated GA4 | A successful form submission; `form_source` identifies the form. |
| `generate_lead` from camera planner | Consent-gated GA4 | A successful saved plan; `form_source=camera-planner`. |
| `lead_created` | Consent-gated SafeTech internal analytics | Event from a form/plan submission; only aggregate and report by source. |
| Phone/WhatsApp clicks | GA4/internal analytics when consented | Intent proxies; not proof of a conversation or purchase. |
| Accepted camera plans | CameraPlan records in Filament | Business records of submitted quote requests; can include contacts that rejected optional analytics consent. |
| Closed jobs | Internal sales records, entered manually | Actual conversion and revenue, separate from web analytics. |

A visitor can trigger several events. Do **not** sum page views + clicks + generated leads as unique users or as orders.

## 3. GA4 verification after deploying planner event

- With analytics consent accepted, submit one valid camera plan and verify **Realtime/DebugView** contains `generate_lead` with `form_source=camera-planner`, `service_slug=security-camera-installation` and a numeric `camera_count`.
- Confirm internal `lead_created` has `meta.source=camera-planner` and the normalized `/camera-planner` URL. Use count by distinct backend plan ID if reporting confirmed saved plans; the analytics event is a separate consented measurement.
- With analytics rejected, successful planner submissions **must still save** but must not emit GA4 or SafeTech internal analytics events.
- Failed HTTP 4xx/5xx submissions must not generate lead conversions.
- No names, phone numbers, email addresses, address, submitted photos or plan geometry may enter GA4 or analytics event metadata.
- If GA4 does not automatically classify `generate_lead` as a key event in the current property, configure it explicitly in the GA4 UI; check for duplicate tags or dual GA/GTM setup before counting conversions.

## 4. Measure the SEO guide → planner journey

For the homeowner CCTV guide, compare:
- GSC impressions/clicks for each language URL.
- Consented GA4 navigation from guide to `/camera-planner` (path analysis).
- Consented `generate_lead` events with `form_source=camera-planner`.
- Business-side accepted CameraPlan requests and the quotes/jobs they eventually become.

GA4 conversion attribution should follow GA4's acquisition information; **do not label all planner leads as Google organic** just because the planner is linked from an SEO page. Users can arrive through Facebook, referrals, direct visits or other channels.

## 5. Production verification

After a reviewed PR is merged and deployed, confirm current production commit and:
- `/robots.txt`, `/sitemap.xml`, `/sitemap-main.xml` and `/sitemap-local-services.xml` return successful responses and expected URLs.
- One visible H1, self-referencing canonical, valid KA/EN/RU hreflang and relevant structured data on representative services, local pages, the guide and planner.
- Correct Georgian city grammar and zero invented customer projects, offices or reviews.
- A completed real plan saved in Filament and optional GA4 conversion recorded when consented.

**CI success is not a guarantee of Google indexing, organic growth or closed deals.**
