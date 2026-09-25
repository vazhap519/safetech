# SafeTech Local SEO: factual coverage and QA

## What the CMS dashboard actually measures

- "6 / 12" means six of twelve published services have at least one publicly visible, noindex=false Local landing. It is not Google's actual indexing report or an overall SEO score.
- "30 indexable" means published and allowed to be indexed on this site; Google can still choose not to index them.
- "29 without real projects" means 29 such landing pages have no linked public project in the CMS. A seeder must never invent projects, reviews, site visits or addresses to turn that number green.
- Drafts, published + noindex, technical indexability and Search Console indexing are separate dimensions.

## Implementation

- Original five-city pages for six earlier priority services (6 × 5 = 30) are restored on fresh empty installations from existing editorial content migrations after services exist. This bootstrap never runs if the CMS already has Local pages and withholds historical title-only project matching.
- Six distinct technical/editorial service + Tbilisi pages complete the catalog (36 total on a fresh install; one additional page for each missing published service).
- New pages include service-specific guidance, KA/EN/RU metadata and text, CTA, FAQ, benefits and generated Service schema.
- Existing five-city pages gain missing EN/RU content and metadata, with exact-key canonical FAQ/benefit translations. No administrator edits, published status or noindex flags are overwritten.
- Add more regional pages only when the area is genuinely serviced and the content adds original local value. Mass-swapping city names risks doorway and duplicate-content issues.
- Duplicate Local Service JSON-LD is removed while editable CMS schema overrides, canonical, hreflang, H1, FAQ, breadcrumbs and sitemap behavior are retained.

## Deploy and verify

The repository's /var/www/safetech/deploy.sh runs SystemContentSeeder on the API server. Do not run migrate:fresh on production. After successful deployment, run:

    cd /var/www/safetech/back
    php artisan safetech:local-seo-audit --strict

The audit reports published service coverage, technical indexable pages, missing localized metadata and public project-link count. It does not mutate data. Strict mode fails on missing coverage or required SEO fields, but does not fail on missing project proof, which requires authentic work.

Verify new service Tbilisi routes and old five-city pages in KA/EN/RU, one H1, correct canonical/robots, sitemap-local-services.xml, Service JSON-LD, consultation CTA and mobile layout. In Google Search Console submit/refresh the sitemap and inspect representative URLs; track real impressions, queries, clicks and leads. Internal 12/12 coverage and SEO QA cannot guarantee position, crawling or indexing. The production count can differ if existing CMS records are unpublished or intentionally set to noindex.

## Proof editorial workflow

Use Local SEO pages > Real Projects to attach a published, actually delivered, service-relevant and city-relevant project only after verifying the job and permission to show it. A published project merely mentioning a city is not sufficient proof. Gather authentic images (with permission), city, scope, equipment and completion details first. Never fabricate a finished project to clear a dashboard warning.


## All-service expansion across six priority regions (September 2026)

PriorityLocalSeoSeeder runs AFTER GoogleBusinessServicesSeeder in production and fresh database seeding. It prepares each current catalogue service for Tbilisi, Bakuriani, Surami, Borjomi, Khashuri and Abastumani. It does not target Batumi.

Each new page includes KA/EN/RU copy and metadata, its actual service description, service-category technical checks, distinct city/property considerations, CTAs, benefits and FAQ. They are planning information, not assertions of completed installations. Existing CMS records are not overwritten or automatically republished, and project associations are never invented.

Already indexable core services receive published local pages. Short noindexed Google Business services receive Local SEO drafts with noindex=true; review the service's actual availability, specific copy, original local value and underlying service SEO setting before publishing them manually. The existing LocalSeoFieldCompletionSeeder can fill missing fields on older records without reversing their editorial indexing decisions.

To verify on the server after normal deploy: cd /var/www/safetech/back && php artisan safetech:local-seo-audit --strict. Check Tbilisi, Bakuriani, Surami and additional region pages in KA/EN/RU. Technical indexability is not Google Search Console indexing.
