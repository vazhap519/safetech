$ErrorActionPreference = 'Stop'

if ([version](php -r "echo PHP_VERSION;") -lt [version]'8.2.0') {
    throw 'Laravel 12 requires PHP 8.2 or newer.'
}

$env:COMPOSER_HOME = Join-Path $env:TEMP 'composer-safetech'
$env:COMPOSER_IPRESOLVE = '4'

composer install --no-interaction --prefer-dist

if (-not (Test-Path '.env')) { Copy-Item '.env.example' '.env' }

if (-not (php -m | Select-String -Pattern '^pdo_pgsql$' -Quiet)) {
    throw 'PostgreSQL PHP extension is not enabled. Enable pdo_pgsql in php.ini before running setup.'
}

php artisan key:generate --force
php artisan migrate --seed --force
php artisan storage:link
php artisan optimize:clear

Write-Host 'SafeTech API and Filament are ready.' -ForegroundColor Green
Write-Host 'Run: php artisan serve' -ForegroundColor Cyan
Write-Host 'Run in another terminal: php artisan queue:work' -ForegroundColor Cyan
