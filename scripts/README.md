# Production scripts

- `deploy-production.sh` performs the SafeTech production release with an isolated Next.js build, atomic activation, automatic rollback, systemd and Nginx installation, and production smoke checks.

Run it through the repository root entrypoint:

```bash
sudo bash deploy.sh
```
