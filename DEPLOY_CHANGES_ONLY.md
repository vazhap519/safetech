# SafeTech Change Deploy Commands

ეს ფაილი არის მხოლოდ ცვლილებების დასადეპლოიებელი სწრაფი ბრძანებები.

## 1. GitHub-ზე branch-იდან `main`-ში გადატანა

```bash
git fetch origin
git checkout main
git pull --ff-only origin main
git merge --ff-only agent-cms-admin-frontend-sync
git push origin main
```

თუ `--ff-only` merge არ გავიდა:

```bash
git fetch origin
git checkout agent-cms-admin-frontend-sync
git rebase origin/main
git push --force-with-lease origin agent-cms-admin-frontend-sync

git checkout main
git pull --ff-only origin main
git merge --ff-only agent-cms-admin-frontend-sync
git push origin main
```

## 2. Server-ზე ცვლილებების deploy

ეს ნაწილი ითვალისწინებს, რომ production deploy იყენებს:

- `/var/www/safetech-source`
- `/var/www/safetech-api`
- `/var/www/safetech-next`

გაუშვი:

```bash
sudo -i
cd /var/www/safetech-source
git checkout main
git pull --ff-only origin main
sudo bash /var/www/safetech-source/deploy.sh
```

## 3. სწრაფი შემოწმება deploy-ის შემდეგ

```bash
systemctl status safetech-frontend safetech-queue --no-pager
curl -I https://safetech.ge/
curl -I https://safetech.ge/shop
curl -I https://safetech.ge/sitemap.xml
curl -I https://safetech.ge/sitemap-product-categories.xml
curl -I https://api.safetech.ge/api/health
```
