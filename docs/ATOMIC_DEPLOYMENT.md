# SafeTech atomic production deployment

The production entrypoint is:

```bash
sudo bash /var/www/safetech/deploy.sh
```

`deploy.sh` delegates to `scripts/deploy-production.sh`.

## Why the deployment is staged

The live Next.js service must never lose its active `.next` directory while a new release is being built. The deployment therefore:

1. builds and validates Next.js in `/var/www/safetech/.deploy/frontend-stage`;
2. keeps the current service and its build untouched during dependency installation, linting, type-checking, and compilation;
3. stops the frontend only after the staged build is complete;
4. atomically swaps `.next` and `node_modules`;
5. requires local HTTP 200 before continuing;
6. installs and validates the repository systemd and Nginx definitions;
7. runs public API, page, asset, upload, and SEO smoke checks;
8. restores the previous frontend automatically when activation or any later release gate fails.

The deployment also holds `/var/lock/safetech-deploy.lock`, preventing two deployments from running simultaneously.

## First recovery after an older failed deployment

Always update the checkout before executing the new entrypoint:

```bash
cd /var/www/safetech
git fetch origin
git checkout main
git pull --ff-only origin main
sudo bash deploy.sh
```

Do not run an older checked-out `deploy.sh` and rely on that running process to update itself.

## Diagnostics on failure

The script prints:

- systemd frontend status;
- the latest frontend journal entries;
- the process listening on port 3000;
- the local HTTP response body and headers.

If the new release fails after the atomic swap, the previous `.next` and `node_modules` are restored and the frontend service is restarted automatically.
