# SafeTech production deployment

The production checkout is deployed in place:

```text
/var/www/safetech/
├── back/
├── frontend/
└── deploy.sh
```

Run the deployment as root. The script verifies that the checkout is clean,
updates the `main` branch with a fast-forward-only pull, installs dependencies,
runs Laravel migrations and production checks, builds Next.js, restarts the
configured services, and performs smoke checks.

```bash
sudo -i
cd /var/www/safetech
git status --short
bash ./deploy.sh
```

If `git status --short` prints any files, do not discard them blindly. Resolve
or preserve those server-side changes before deploying.

The defaults match the directory layout above. Override them only when the
server uses different values:

```bash
SAFETECH_FRONTEND_SERVICE=my-frontend.service \
SAFETECH_QUEUE_SERVICE=my-queue.service \
bash /var/www/safetech/deploy.sh
```

Other supported overrides are:

- `SAFETECH_PROJECT_DIR`
- `SAFETECH_BACKEND_DIR`
- `SAFETECH_FRONTEND_DIR`
- `SAFETECH_STATIC_DIR`
- `SAFETECH_BRANCH`
- `SAFETECH_REMOTE`
- `SAFETECH_SITE_URL`
- `SAFETECH_API_URL`
- `SAFETECH_WEB_USER`
- `SAFETECH_WEB_GROUP`
- `SAFETECH_NGINX_CACHE_DIR`

The script deliberately does not overwrite Nginx or systemd configuration.
