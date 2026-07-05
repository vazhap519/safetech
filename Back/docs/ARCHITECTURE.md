# SafeTech API and CMS architecture

The contact-lead flow follows a small layered architecture:

1. `StoreContactLeadRequest` normalizes and validates untrusted HTTP input.
2. `LeadData` transfers validated values without coupling the domain to HTTP.
3. `CreateLead` owns the use case and depends on the `LeadRepository` contract.
4. `EloquentLeadRepository` persists through the `ContactLead` model.
5. `LeadCreated` decouples persistence from email and CRM integrations.
6. Queued listeners deliver the notification and optional CRM webhook.

Public content uses repository contracts, cached Eloquent repositories, API Resources and model-driven cache invalidation. Filament manages services, projects, team members, partners, testimonials, FAQs, global settings, administrators and incoming leads at `/admin`.

## Local setup

Laravel 12 requires PHP 8.2 or newer.

```bash
.\setup.ps1
```

Set `NEXT_PUBLIC_API_URL=http://localhost:8000/api` in the frontend `.env.local`.
For production, configure SMTP, `LEADS_NOTIFICATION_EMAIL`, `FRONTEND_URLS`, and optionally the CRM webhook variables.

## API

- `GET /api/health`
- `POST /api/contact-leads`
- `GET /api/services`
- `GET /api/services/{slug}`
- `GET /api/projects`
- `GET /api/projects/{slug}`
- `GET /api/content`

The public lead endpoint is protected by validation, a honeypot field, privacy consent, CORS, payload limits at the web server, and a five-requests-per-minute limiter.
