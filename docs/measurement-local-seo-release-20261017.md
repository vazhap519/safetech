# Measurement and local SEO quality release

This change combines the relevant fixes proposed in #101, #104, #118 and #119 on the current main branch. Production deployment is separate.

## Resulting behavior

- Direct GA4 takes precedence over GTM. The external Google tag, Meta Pixel and trusted footer counter load after consent. Initial and client navigation page views use one application path per provider, with a previous-page referrer.
- Phone, email and WhatsApp clicks have distinct events. They are contact intent, not completed calls or orders. `generate_lead` is not repeated for an idempotent response with `data.replayed=true`.
- Changing the privacy choice sends Google denial and Meta revocation, disables the active direct GA4 property, removes stored campaign attribution and stops application event helpers until consent is granted again.
- Local SEO summary links, detail availability and sitemap locale eligibility agree. Hreflang lists only available translations.
- Installed equipment uses `Thing` within project context. Image sitemap includes cover and gallery images, deduplicates identical page/image pairs and excludes noindex projects.
- Privacy lists the active Google, Meta and TOP.GE integrations from the same settings used by the loaders, with a working choice control.

## Tbilisi content update

The versioned migration and non-destructive seeder replace only the known generated access-control copy on the CCTV/Tbilisi landing. They preserve publication/noindex/canonical flags, other records and existing project links. A private JSON backup is written to `back/storage/app/private/content-backups/2026-10-17-tbilisi-cctv-{id}.json` before rewriting. A later manually edited body is not overwritten by repeated deployments.

Project evidence was checked through the public API on 17 October 2026 (Asia/Tbilisi):

- `6-kameris-videometvalqureobis-sistema-meurneobis-obiektze-varketili`: six-camera installation in Varketili, Tbilisi.
- `videometvalqureobis-sistemis-aghdgena-gakhokidzis-kuchaze`: replacement of damaged cameras, DVR and connectors in Tbilisi.
- `tbilisshi-tp-link-vigi-full-color-kamerebis-montazhi`: four TP-Link VIGI cameras at the Iosebidze Street salon.

Only existing, publicly visible, indexable projects with those exact slugs and a matching Tbilisi city are attached. No projects or photographs are created or copied from another business. Public project images already supply the proof cards. The local CTA prefills the service and location in the existing enquiry form.

## Deployment and account checks

Use the existing server `./deploy.sh` procedure after this PR is merged. It runs migrations, refreshes content and rebuilds the frontend. No destructive content reset is needed. The migration rollback intentionally leaves editorial content alone; restore the private backup explicitly only if appropriate.

The automatic GitHub deployment remains blocked until the owner configures the real `SAFETECH_DEPLOY_SSH_KEY` and pinned `SAFETECH_DEPLOY_KNOWN_HOSTS` secrets. The existing `scripts/bootstrap-github-actions-deploy.sh` prepares the restricted server account. This PR does not invent credentials, weaken host verification or turn a blocked deploy into success.

For **manual page views**, disable **Page changes based on browser history events** in GA4 Admin → Data streams → Web stream → Enhanced measurement → Page views → Show advanced settings. `send_page_view:false` disables the config page view but does not disable Enhanced Measurement's separate history listener. Also inspect Enhanced Measurement's form interactions if unexpected `form_start` counts persist. Those account settings cannot be confirmed by browser transport stubs.

Official reference: https://developers.google.com/analytics/devguides/collection/ga4/views

After deployment, use Tag Assistant/DebugView to confirm one first consented page view, one per navigation, the previous referrer and a single successful enquiry. Verify the selected key event is not also imported as a second primary conversion through another tag. Keep phone/WhatsApp clicks distinct from submitted requests.

Browser regressions stub external analytics transports and enquiry submission: they do not send test messages to customers, produce ad traffic or prove SMTP inbox delivery. Actual inbox receipt, Google indexing and ranking changes require production/account verification.
