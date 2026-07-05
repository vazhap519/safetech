# SafeTech Deployment Checklist

## Frontend

1. Copy [`.env.production.example`](/F:/safetech/.env.production.example) to `.env.production`.
2. Set `NEXT_PUBLIC_API_URL` to the live Laravel API URL.
3. Set `NEXT_PUBLIC_SITE_URL` to the canonical public domain.
4. Add `NEXT_PUBLIC_GA_ID` and `GOOGLE_SITE_VERIFICATION` if analytics/search console are used.
5. Build with `npm run build`.

## Backend

1. Copy [`Back/.env.production.example`](/F:/safetech/Back/.env.production.example) to `Back/.env`.
2. Set a real `APP_KEY` with `php artisan key:generate`.
3. Configure MySQL credentials and set `FILESYSTEM_DISK=public`.
4. Configure real SMTP credentials. `LEADS_NOTIFICATION_EMAIL` is set to `safetechgeorgia@gmail.com` and is used as the priority recipient for all site forms.
5. Run:

```bash
cd Back
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

## Post-Deploy Checks

1. Open `/`, `/contact`, `/services/cctv`, `/projects`.
2. Submit every form and confirm the request is stored in admin and delivered to `safetechgeorgia@gmail.com`.
3. Check `https://your-domain/sitemap.xml` and `https://your-domain/robots.txt`.
4. Confirm uploaded branding assets load from admin-managed content.
5. If CRM forwarding is enabled, run a queue worker:

```bash
cd Back
php artisan queue:work --tries=1
```
