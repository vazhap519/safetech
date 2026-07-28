# SafeTech Change Deploy Commands

Quick commands for updating production from the single `main` branch.
The only deployment script that should be executed on the server is `deploy.sh`.

## 1. Update the source checkout on the server

```bash
sudo -i
cd /var/www/safetech-source
git fetch --prune origin
git checkout main
git pull --ff-only origin main
```

## 2. Run the deployment script

```bash
sudo bash /var/www/safetech-source/deploy.sh
```

## 3. Quick checks after deploy

```bash
systemctl status safetech-frontend safetech-queue --no-pager
curl -I https://safetech.ge/
curl -I https://safetech.ge/shop
curl -I https://safetech.ge/sitemap.xml
curl -I https://safetech.ge/sitemap-product-categories.xml
curl -I https://safetech.ge/sitemap-products.xml
curl -I https://api.safetech.ge/api/health
```
