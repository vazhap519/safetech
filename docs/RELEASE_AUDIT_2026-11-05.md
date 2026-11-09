# Release verification — 5 November 2026

## Verified changes

PR #128 extends the frontend's legacy redirect registry. This follow-up adds the
missing `os-installation-support` backend alias, removes an unused proxy import,
and checks exact equality of the frontend and backend redirect registries.
The HTTP matrix now uses all backend aliases for plain paths, query links and
Tbilisi paths, including explicit `/ka`, `/en`, `/ru` and unprefixed Georgian.

## Verification completed locally

- Next.js 16.4.3: lint, TypeScript and production build passed. Final lint has no warnings.
- Laravel: 330 tests passed, 3,360 assertions, isolated in-memory SQLite database.
- Migration regression: a synthetic fixture with 17 projects and 204 local rows,
  historical media ownership and seeded quote prices survives two consolidation
  runs with exact table snapshots unchanged. Foreign-key checks pass; seven
  services remain published. Existing collision tests cover quote prices and
  merging project associations. These fixtures are not a production database copy.
- All 12 HTTP alias matrix tests passed (all registered aliases, four prefixes,
  three URL forms). Location and query preservation checked.
- Composer locked dependency audit and npm production dependency audit passed
  with no advisories reported.
- Deployment shell syntax and `git diff --check` passed.

## Read-only production observations

- Production API identifies commit `90ad73e4ae9ccaf4c51ec6b55e6b22a277a5d8dd`.
- Public services API returns the seven canonical services.
- Public local landing API returns 42 eligible rows; local sitemap has 126 URLs
  across three languages. This does not establish whether all 204 historical
  database rows are present. Do not delete archives or regenerate content.
- Projects API returns 18 rows; projects sitemap contains 51 URLs (17 per
  language). Sitemap eligibility differs from the full public API list and
  requires editorial review before changing visibility.
- Live SEO smoke and sitemap audit passed for its capped sample of 250
  indexable sitemap URLs. This is not a full crawl of every historical page.
- Health is `degraded`: database OK, worker OK, zero pending and one failed job.
  The failed job's exception and retry safety require server inspection.

## Deployment gate and rollback plan

No production writes or deployment were performed during this review. No server
SSH identity or production database snapshot is available in this workspace.
The new commit must pass CI and the following checks before merge/deployment:

1. Obtain a consistent database backup and media/storage backup, recording the
   deployed SHA and configuration. Use the configured database driver's native
   backup tooling (SQLite online backup or PostgreSQL `pg_dump`). Keep credentials
   and backups outside Git and the public web root.
2. Restore that backup into a separate staging database on the same engine and
   version. Record counts, IDs and content/price/media hashes for projects,
   local_service_landings, media, quote_catalog_items and their pivot tables.
3. Run `php artisan migrate --force`, then
   `php artisan safetech:seven-service-preflight --audit-collisions` and
   `php artisan safetech:seven-service-preflight --execute` only on the clone.
   Compare snapshots; permit only documented service publication changes,
   non-colliding landing ownership changes and additive project associations.
   Confirm every original row, photograph and price remains; verify 204 original
   landing IDs. Run the consolidation twice to verify idempotence.
4. Run the full test/SEO/browser matrix against that clone. Check all seven
   public service pages and locale variants. Investigate the failed queue job.
5. Deploy the exact approved SHA via the existing protected deployment workflow
   only after the restore rehearsal succeeds. The existing deployment script
   automatically restores frontend build/node_modules on failed activation.
6. For a backend/database failure, pause writes and queue workers, restore the
   recorded application release plus the verified database/media backups as a
   matched set, clear/rebuild Laravel caches and restart services. Recheck API
   health, queue status, redirects and content hashes before reopening writes.
   Do not use destructive reseeding or `migrate:fresh` in production. Frontend
   automatic rollback alone does not restore backend code or database changes.

PR remains a draft until the production-data rehearsal and deployment gates are
satisfied. Test success alone does not establish production data integrity.
