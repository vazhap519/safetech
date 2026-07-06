# SafeTech Deployment Checklist

## Critical Fixes

1. If `https://api.safetech.ge` shows the default Laravel welcome page, the vhost is pointing at the wrong directory.
2. The API domain must point to `Back/public`, not to the Laravel project root.
3. The public site must be served over HTTPS with `http2` enabled, otherwise Lighthouse will continue reporting `http/1.1`.
4. Use the sample configs in [deploy/nginx/safetech.example.conf](/F:/safetech/deploy/nginx/safetech.example.conf) and [deploy/systemd](/F:/safetech/deploy/systemd).

## Frontend

1. Copy [`.env.production.example`](/F:/safetech/.env.production.example) to `.env.production`.
2. Set `BACKEND_API_URL` to the internal backend address that the frontend server can always reach, for example `http://127.0.0.1:8000/api` or your private VPC/LAN URL.
3. Set `NEXT_PUBLIC_API_URL=https://api.safetech.ge/api` so public asset URLs and external API references still point at the public API host.
4. Set `NEXT_PUBLIC_SITE_URL=https://safetech.ge`.
5. Set `NEXT_PUBLIC_GA_ID` and `GOOGLE_SITE_VERIFICATION` if analytics and Search Console are used.
6. If `BACKEND_API_URL` is missing or points to an unreachable hostname, the frontend can silently fall back to local placeholder content. This is the main failure mode behind mixed/old content on production.
7. Install dependencies and build:

```bash
cd /var/www/safetech
npm ci
npm run build
```

8. Run the frontend with a process manager or the provided systemd service:

```bash
sudo cp deploy/systemd/safetech-frontend.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now safetech-frontend
```

## Backend

1. Copy [`Back/.env.production.example`](/F:/safetech/Back/.env.production.example) to `Back/.env`.
2. Set a real `APP_KEY` with `php artisan key:generate`.
3. Configure MySQL credentials and `FILESYSTEM_DISK=public`.
4. Configure SMTP credentials.
5. `LEADS_NOTIFICATION_EMAIL` should remain `safetechgeorgia@gmail.com` unless you deliberately change the lead recipient.
6. Run:

```bash
cd /var/www/safetech/Back
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

7. If CRM forwarding is enabled, run the queue worker:

```bash
sudo cp /var/www/safetech/deploy/systemd/safetech-queue.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now safetech-queue
```

## Nginx

1. Copy [deploy/nginx/safetech.example.conf](/F:/safetech/deploy/nginx/safetech.example.conf) into your Nginx sites config.
2. Update certificate paths, PHP-FPM socket path, and the real project path if needed.
3. Make sure the SSL vhosts use `listen 443 ssl http2;`.
4. Test and reload:

```bash
sudo nginx -t
sudo systemctl reload nginx
```

## Post-Deploy Checks

1. Open `https://safetech.ge/`, `https://safetech.ge/contact`, `https://safetech.ge/services/cctv`, and `https://safetech.ge/projects`.
2. Open `https://api.safetech.ge/api/services`.
3. Confirm `https://api.safetech.ge` no longer shows the Laravel welcome page.
4. Submit every form and confirm the request is stored in admin and delivered to `safetechgeorgia@gmail.com`.
5. In the contact form, switch services and verify the dynamic fields change correctly.
6. Confirm branding assets, favicon, logo, and footer social links are loading from admin-managed data.
7. Confirm the homepage does not show raw icon names like `videocam`, `dns`, `verified`, or English placeholder eyebrow text unless that language is intentionally selected.
8. Check `https://safetech.ge/sitemap.xml` and `https://safetech.ge/robots.txt`.
9. Confirm the site negotiates `h2` or `h3` in the browser network panel.
