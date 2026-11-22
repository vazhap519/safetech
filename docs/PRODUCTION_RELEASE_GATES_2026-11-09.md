# Production release gates — 2026-11-09

This document is an **operator checklist**, not permission to deploy. Production remains unchanged by this branch.

## Verified observations
- Nginx upstream for public site: 127.0.0.1:3000, Next.js 16.3.8; a separate 16.4.3 process listens on port 3003, but **is not verified as isolated staging**.
- Both Laravel and frontend production environment files have mode 0600 and owner www-data; the non-privileged audit user cannot read them.
- Public website and API root returned HTTP 200. This is a liveness check, not end-to-end acceptance.
- No verified Vultr snapshot/backup exists.
- PR #128 reports 42 eligible local SEO rows (126 localized URLs) through the public API, **not** a verified inventory of the expected 204 historical records. One failed queue job is reported.

## Blocking gates (must all be met before production merge/deploy)
1. **Database inventory:** read-only PostgreSQL queries must establish the exact local SEO table, row counts by publication/indexing status and locale, projects, quote catalog and media relations. Do not equate 42 published/eligible rows with total rows or delete the remainder.
2. **Queue:** inspect the failed job's class, failure timestamp and sanitized exception. Determine whether it is a stale failure or an ongoing issue. Do not retry, flush or delete failed jobs until the root cause is understood.
3. **Matched backups:** create an off-host native PostgreSQL backup and consistent media/configuration backup, with a documented timestamp and integrity checks. Never commit or paste .env contents, passwords or job payloads.
4. **Restore rehearsal:** restore into an isolated environment with non-production credentials, no outbound email, queue workers, scheduled tasks, payment integrations or external webhooks.
5. **Migration rehearsal:** run pending Laravel migrations and seven-service consolidation only on the isolated restore; compare full before/after record identities and relationships for local pages, projects, prices, media, redirects and published services.
6. **Rollback:** record a matched DB/media rollback and frontend rollback plan, including restore time and operator.
7. **Acceptance:** confirm all seven canonical service URLs and legacy redirects in KA/EN/RU, local sitemap and hreflang, forms, admin, API, uploads and queues; require green CI on final head.

## Safe commands for an authorized operator (read-only)
Run from a Linux SSH session. These commands do **not** perform deployment, migrations, seeders, backups or queue mutations:

```bash
stat -c '%a %U:%G %n' /var/www/safetech/back/.env /var/www/safetech/frontend/.env.production
curl -sS -o /dev/null -w 'Website HTTP: %{http_code}\n' --max-time 15 https://safetech.ge/
curl -sS -o /dev/null -w 'API HTTP: %{http_code}\n' --max-time 15 https://api.safetech.ge/
systemctl is-active nginx php8.3-fpm postgresql safetech-queue.service
df -h /
```

Inspect the queue with a privileged operator **without pasting raw payloads or secrets into a ticket**. Do not run `queue:retry`, `queue:flush`, `migrate`, `db:seed` or `deploy.sh` in production as part of diagnostics.

## Release status
**BLOCKED** until evidence for gates 1–7 is attached. Synthetic SQLite tests and a healthy HTTP 200 cannot substitute for a PostgreSQL production-copy rehearsal.
