# Production scripts

- `deploy-production.sh` performs the SafeTech production release with an isolated Next.js build, atomic activation, automatic rollback, systemd and Nginx installation, and production smoke checks.

Run it through the repository root entrypoint:

```bash
sudo bash deploy.sh
```

For a deployment that survives SSH disconnects, use the same root entrypoint
through a transient systemd service:

```bash
sudo systemd-run --unit=safetech-deploy --collect \
  --property=WorkingDirectory=/var/www/safetech \
  /usr/bin/bash /var/www/safetech/deploy.sh
```
