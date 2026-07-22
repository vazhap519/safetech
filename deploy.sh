#!/usr/bin/env bash

set -Eeuo pipefail

SOURCE_DIR="${SAFETECH_SOURCE_DIR:-/var/www/safetech-source}"
API_DIR="${SAFETECH_API_DIR:-/var/www/safetech-api}"
NEXT_DIR="${SAFETECH_NEXT_DIR:-/var/www/safetech-next}"
NEXT_STATIC_DIR="/var/www/safetech-static"
WEB_USER="${SAFETECH_WEB_USER:-www-data}"
WEB_GROUP="${SAFETECH_WEB_GROUP:-www-data}"
SYSTEMD_DIR="${SAFETECH_SYSTEMD_DIR:-/etc/systemd/system}"
NGINX_CONFIG_SOURCE="${SOURCE_DIR}/frontend/deploy/nginx/safetech.example.conf"
NGINX_SITE_AVAILABLE="${SAFETECH_NGINX_SITE_AVAILABLE:-/etc/nginx/sites-available/safetech.conf}"
NGINX_SITE_ENABLED="${SAFETECH_NGINX_SITE_ENABLED:-/etc/nginx/sites-enabled/safetech.conf}"
NGINX_CACHE_DIR="/var/cache/nginx/safetech"
FRONTEND_SERVICE="safetech-frontend.service"
QUEUE_SERVICE="safetech-queue.service"
NEXT_RELEASE_DIR=""
NEXT_PREVIOUS_DIR=""

safe_remove_frontend_temp() {
    local target="$1"

    case "${target}" in
        "${NEXT_DIR}.release."*|"${NEXT_DIR}.previous."*|"${NEXT_DIR}.failed."*)
            rm -rf -- "${target}"
            ;;
        *)
            echo "Refusing to remove unexpected deployment path: ${target}" >&2
            return 1
            ;;
    esac
}

clear_nginx_html_cache() {
    case "${NGINX_CACHE_DIR}" in
        /var/cache/nginx/*)
            find "${NGINX_CACHE_DIR}" -mindepth 1 -delete
            ;;
        *)
            echo "Refusing to clear unexpected Nginx cache path: ${NGINX_CACHE_DIR}" >&2
            return 1
            ;;
    esac
}

cleanup() {
    local exit_status=$?

    if [[ "${exit_status}" -ne 0 && -n "${NEXT_PREVIOUS_DIR}" && -d "${NEXT_PREVIOUS_DIR}" ]]; then
        local failed_dir="${NEXT_DIR}.failed.$$"

        systemctl stop "${FRONTEND_SERVICE}" || true

        if [[ -e "${failed_dir}" ]]; then
            echo "Cannot roll back because the failed-release path already exists: ${failed_dir}" >&2
        elif mv "${NEXT_DIR}" "${failed_dir}" && mv "${NEXT_PREVIOUS_DIR}" "${NEXT_DIR}"; then
            NEXT_PREVIOUS_DIR=""
            systemctl start "${FRONTEND_SERVICE}" || true
            clear_nginx_html_cache || true
            systemctl reload nginx || true
            safe_remove_frontend_temp "${failed_dir}" || true
            echo "Deployment validation failed; the previous frontend release was restored." >&2
        else
            echo "Automatic frontend rollback failed; preserved deployment directories for manual recovery." >&2
        fi
    fi

    if [[ -n "${NEXT_RELEASE_DIR}" && -d "${NEXT_RELEASE_DIR}" ]]; then
        safe_remove_frontend_temp "${NEXT_RELEASE_DIR}"
    fi

    return "${exit_status}"
}

finalize_frontend_release() {
    if [[ -n "${NEXT_PREVIOUS_DIR}" && -d "${NEXT_PREVIOUS_DIR}" ]]; then
        safe_remove_frontend_temp "${NEXT_PREVIOUS_DIR}"
        NEXT_PREVIOUS_DIR=""
    fi
}

activate_frontend_release() {
    local failed_dir="${NEXT_DIR}.failed.$$"
    local previous_dir="${NEXT_DIR}.previous.$$"
    local frontend_active=false

    for temporary_dir in "${previous_dir}" "${failed_dir}"; do
        if [[ -e "${temporary_dir}" ]]; then
            echo "Frontend deployment path already exists: ${temporary_dir}" >&2
            exit 1
        fi
    done

    NEXT_PREVIOUS_DIR="${previous_dir}"
    systemctl stop "${FRONTEND_SERVICE}" || true

    if ! mv "${NEXT_DIR}" "${NEXT_PREVIOUS_DIR}"; then
        echo "Unable to preserve the current frontend release." >&2
        exit 1
    fi

    if ! mv "${NEXT_RELEASE_DIR}" "${NEXT_DIR}"; then
        mv "${NEXT_PREVIOUS_DIR}" "${NEXT_DIR}"
        NEXT_PREVIOUS_DIR=""
        systemctl start "${FRONTEND_SERVICE}"
        echo "Unable to activate the new frontend release." >&2
        exit 1
    fi

    NEXT_RELEASE_DIR=""

    if systemctl start "${FRONTEND_SERVICE}"; then
        sleep 3

        for _attempt in {1..10}; do
            if systemctl is-active --quiet "${FRONTEND_SERVICE}"; then
                frontend_active=true
                break
            fi

            sleep 1
        done
    fi

    if [[ "${frontend_active}" != true ]]; then
        systemctl stop "${FRONTEND_SERVICE}" || true

        if mv "${NEXT_DIR}" "${failed_dir}" && mv "${NEXT_PREVIOUS_DIR}" "${NEXT_DIR}"; then
            NEXT_PREVIOUS_DIR=""
            systemctl start "${FRONTEND_SERVICE}" || true
            safe_remove_frontend_temp "${failed_dir}" || true
            echo "Frontend activation failed; the previous release was restored." >&2
        else
            echo "Frontend activation and automatic rollback failed; preserved deployment directories for manual recovery." >&2
        fi

        journalctl -u "${FRONTEND_SERVICE}" -n 50 --no-pager >&2 || true
        exit 1
    fi
}

trap cleanup EXIT

if [[ "${EUID}" -ne 0 ]]; then
    echo "Run this deployment script with sudo/root privileges." >&2
    exit 1
fi

required_commands=(git rsync php composer node npm systemctl journalctl curl install nginx ln awk grep tr tail dirname find mv rm sleep cmp sed sort)

for command_name in "${required_commands[@]}"; do
    if ! command -v "${command_name}" >/dev/null 2>&1; then
        echo "Missing required command: ${command_name}" >&2
        exit 1
    fi
done

for deployment_dir in "${SOURCE_DIR}" "${API_DIR}" "${NEXT_DIR}" "${NEXT_STATIC_DIR}"; do
    if [[ "${deployment_dir}" != /* || "${deployment_dir}" == "/" ]]; then
        echo "Deployment directories must be absolute non-root paths: ${deployment_dir}" >&2
        exit 1
    fi

done

source_dir_normalized="${SOURCE_DIR%/}"
api_dir_normalized="${API_DIR%/}"
next_dir_normalized="${NEXT_DIR%/}"
static_dir_normalized="${NEXT_STATIC_DIR%/}"

if [[ "${source_dir_normalized}" == "${api_dir_normalized}" \
    || "${source_dir_normalized}" == "${next_dir_normalized}" \
    || "${source_dir_normalized}" == "${static_dir_normalized}" \
    || "${api_dir_normalized}" == "${next_dir_normalized}" \
    || "${api_dir_normalized}" == "${static_dir_normalized}" \
    || "${next_dir_normalized}" == "${static_dir_normalized}" ]]; then
    echo "Source, API, frontend, and static deployment directories must be distinct." >&2
    exit 1
fi

for required_file in \
    "${SOURCE_DIR}/.git/HEAD" \
    "${SOURCE_DIR}/back/artisan" \
    "${SOURCE_DIR}/frontend/package.json" \
    "${NGINX_CONFIG_SOURCE}" \
    "${SOURCE_DIR}/frontend/deploy/systemd/${FRONTEND_SERVICE}" \
    "${SOURCE_DIR}/frontend/deploy/systemd/${QUEUE_SERVICE}" \
    "${API_DIR}/.env" \
    "${NEXT_DIR}/.env.production"; do
    if [[ ! -f "${required_file}" ]]; then
        echo "Missing required file: ${required_file}" >&2
        exit 1
    fi
done

# Embedded PHP is intentionally single-quoted to prevent shell expansion.
# shellcheck disable=SC2016
php -r '
$missing = [];

if (PHP_VERSION_ID < 80300) {
    $missing[] = "PHP 8.3 or newer";
}

if (! extension_loaded("pdo_pgsql")) {
    $missing[] = "pdo_pgsql";
}

$supportsWebp = extension_loaded("imagick")
    || (extension_loaded("gd") && function_exists("imagewebp"));

if (! $supportsWebp) {
    $missing[] = "imagick or GD with WebP support";
}

if ($missing !== []) {
    fwrite(STDERR, "Missing production PHP capability: ".implode(", ", $missing).PHP_EOL);
    exit(1);
}
'

if [[ -n "$(git -C "${SOURCE_DIR}" status --porcelain)" ]]; then
    echo "Deployment source has uncommitted changes: ${SOURCE_DIR}" >&2
    exit 1
fi

git -C "${SOURCE_DIR}" fetch --prune origin
git -C "${SOURCE_DIR}" checkout main
git -C "${SOURCE_DIR}" pull --ff-only origin main

install -o root -g root -m 0644 \
    "${SOURCE_DIR}/frontend/deploy/systemd/${FRONTEND_SERVICE}" \
    "${SYSTEMD_DIR}/${FRONTEND_SERVICE}"
install -o root -g root -m 0644 \
    "${SOURCE_DIR}/frontend/deploy/systemd/${QUEUE_SERVICE}" \
    "${SYSTEMD_DIR}/${QUEUE_SERVICE}"
systemctl daemon-reload
systemctl enable "${FRONTEND_SERVICE}" "${QUEUE_SERVICE}" >/dev/null

mkdir -p \
    "${API_DIR}/storage/app/public" \
    "${API_DIR}/storage/framework/cache" \
    "${API_DIR}/storage/framework/sessions" \
    "${API_DIR}/storage/framework/views" \
    "${API_DIR}/storage/logs" \
    "${API_DIR}/bootstrap/cache" \
    "${NEXT_DIR}" \
    "${NEXT_STATIC_DIR}"

install -d -o "${WEB_USER}" -g "${WEB_GROUP}" -m 0750 \
    "${NGINX_CACHE_DIR}"

install -d -o root -g root -m 0755 \
    "$(dirname "${NGINX_SITE_AVAILABLE}")" \
    "$(dirname "${NGINX_SITE_ENABLED}")" \
    /var/www/_letsencrypt
install -o root -g root -m 0644 \
    "${NGINX_CONFIG_SOURCE}" \
    "${NGINX_SITE_AVAILABLE}"
ln -sfn "${NGINX_SITE_AVAILABLE}" "${NGINX_SITE_ENABLED}"
nginx -t

rsync -a --delete-after \
    --exclude='.env' \
    --exclude='vendor/' \
    --exclude='storage/' \
    --exclude='bootstrap/cache/' \
    "${SOURCE_DIR}/back/" "${API_DIR}/"

NEXT_RELEASE_DIR="${NEXT_DIR}.release.$$"

if [[ -e "${NEXT_RELEASE_DIR}" ]]; then
    echo "Frontend release path already exists: ${NEXT_RELEASE_DIR}" >&2
    exit 1
fi

install -d -o root -g root -m 0755 "${NEXT_RELEASE_DIR}"
rsync -a --delete-after \
    --exclude='.env.production' \
    --exclude='node_modules/' \
    --exclude='.next/' \
    "${SOURCE_DIR}/frontend/" "${NEXT_RELEASE_DIR}/"
install -o "${WEB_USER}" -g "${WEB_GROUP}" -m 0640 \
    "${NEXT_DIR}/.env.production" \
    "${NEXT_RELEASE_DIR}/.env.production"

composer --working-dir="${API_DIR}" install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader
composer --working-dir="${API_DIR}" audit --locked --no-dev --no-interaction

php "${API_DIR}/artisan" config:clear
php "${API_DIR}/artisan" route:clear
php "${API_DIR}/artisan" view:clear
php "${API_DIR}/artisan" cms:production-check
php "${API_DIR}/artisan" migrate --force
php "${API_DIR}/artisan" db:seed --class=AdminUserSeeder --force
php "${API_DIR}/artisan" db:seed --class=SystemContentSeeder --force
php "${API_DIR}/artisan" cms:remove-demo-content --force
php "${API_DIR}/artisan" cache:clear
php "${API_DIR}/artisan" storage:link --force
php "${API_DIR}/artisan" optimize
php "${API_DIR}/artisan" queue:restart

npm --prefix "${NEXT_RELEASE_DIR}" ci
npm --prefix "${NEXT_RELEASE_DIR}" audit --omit=dev --audit-level=high
npm --prefix "${NEXT_RELEASE_DIR}" run check
npm --prefix "${NEXT_RELEASE_DIR}" prune --omit=dev

if [[ ! -s "${NEXT_RELEASE_DIR}/.next/BUILD_ID" ]]; then
    echo "Next.js production build did not create a BUILD_ID." >&2
    exit 1
fi

rsync -a --checksum \
    "${NEXT_RELEASE_DIR}/.next/static/" \
    "${NEXT_STATIC_DIR}/"

static_file_count=0
static_css_count=0
static_js_count=0

while IFS= read -r -d '' built_asset; do
    relative_asset="${built_asset#"${NEXT_RELEASE_DIR}/.next/static/"}"
    shared_asset="${NEXT_STATIC_DIR}/${relative_asset}"

    if [[ ! -f "${shared_asset}" ]] || ! cmp --silent "${built_asset}" "${shared_asset}"; then
        echo "Static asset synchronization failed: ${relative_asset}" >&2
        exit 1
    fi

    static_file_count=$((static_file_count + 1))

    case "${relative_asset}" in
        *.css) static_css_count=$((static_css_count + 1)) ;;
        *.js) static_js_count=$((static_js_count + 1)) ;;
    esac
done < <(find "${NEXT_RELEASE_DIR}/.next/static" -type f -print0)

if [[ "${static_file_count}" -eq 0 || "${static_css_count}" -eq 0 || "${static_js_count}" -eq 0 ]]; then
    echo "Next.js build is missing required static assets (files=${static_file_count}, css=${static_css_count}, js=${static_js_count})." >&2
    exit 1
fi

find "${NEXT_STATIC_DIR}" -type f -mtime +30 -delete
find "${NEXT_STATIC_DIR}" -mindepth 1 -type d -empty -delete
chown -R root:root "${NEXT_STATIC_DIR}"

chown -R "${WEB_USER}:${WEB_GROUP}" \
    "${API_DIR}/storage" \
    "${API_DIR}/bootstrap/cache" \
    "${NEXT_RELEASE_DIR}/.next"

activate_frontend_release
systemctl restart "${QUEUE_SERVICE}"
clear_nginx_html_cache
systemctl reload nginx

health_json="$(curl --fail --silent --show-error --retry 10 --retry-connrefused --retry-delay 2 \
    "https://api.safetech.ge/api/health")"
calculator_json="$(curl --fail --silent --show-error --retry 5 --retry-delay 2 \
    "https://api.safetech.ge/api/service-calculator/profiles?locale=ka")"

for api_payload in "${health_json}" "${calculator_json}"; do
    if ! php -r '
        json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
    ' <<< "${api_payload}"; then
        echo "A required production API returned invalid JSON." >&2
        exit 1
    fi
done

curl --fail --silent --show-error --retry 10 --retry-connrefused --retry-delay 2 \
    "https://safetech.ge/service-calculator" >/dev/null
sitemap_index="$(curl --fail --silent --show-error --retry 5 --retry-delay 2 \
    "https://safetech.ge/sitemap.xml")"

warm_paths=(
    "/"
    "/about"
    "/contact"
    "/services"
    "/service-calculator"
    "/projects"
    "/blog"
    "/privacy"
    "/en"
    "/en/about"
    "/en/contact"
    "/en/services"
    "/en/service-calculator"
    "/en/projects"
    "/en/blog"
    "/en/privacy"
    "/ru"
    "/ru/about"
    "/ru/contact"
    "/ru/services"
    "/ru/service-calculator"
    "/ru/projects"
    "/ru/blog"
    "/ru/privacy"
)

for path in "${warm_paths[@]}"; do
    page_html="$(curl --fail --silent --show-error --compressed --retry 3 --retry-delay 1 \
        -H "Accept: text/html" \
        "https://safetech.ge${path}")"
    page_is_noindex=false

    if grep -Eq '<meta name="robots" content="[^"]*noindex' <<< "${page_html}"; then
        page_is_noindex=true
    fi

    if [[ "${page_is_noindex}" == false ]]; then
        if ! grep -Eq '<meta name="description" content="[^"]{20,}"' <<< "${page_html}"; then
            echo "Missing or too-short meta description: https://safetech.ge${path}" >&2
            exit 1
        fi

        if ! grep -Eq '<h1([[:space:]][^>]*)?>' <<< "${page_html}"; then
            echo "Missing page heading: https://safetech.ge${path}" >&2
            exit 1
        fi

        h1_count="$(grep -Eo '<h1([[:space:]][^>]*)?>' <<< "${page_html}" | awk 'END { print NR + 0 }')"

        if [[ "${h1_count}" -ne 1 ]]; then
            echo "Expected one H1, found ${h1_count}: https://safetech.ge${path}" >&2
            exit 1
        fi
    fi

    if ! grep -Eq '<link rel="canonical" href="https://safetech.ge[^\"]*"' <<< "${page_html}"; then
        echo "Missing production canonical URL: https://safetech.ge${path}" >&2
        exit 1
    fi

    hreflang_count="$(grep -Eo 'hreflang="(ka-GE|en-US|ru-RU|x-default)"' <<< "${page_html}" | awk 'END { print NR + 0 }')"

    if [[ "${hreflang_count}" -lt 4 ]]; then
        echo "Incomplete language alternates: https://safetech.ge${path}" >&2
        exit 1
    fi
done

home_html="$(curl --fail --silent --show-error --compressed \
    -H 'Accept: text/html' \
    'https://safetech.ge/')"
static_asset_paths="$(grep -Eo '/_next/static/[^"[:space:]]+\.(js|css)' <<< "${home_html}" | sort -u || true)"

if [[ -z "${static_asset_paths}" ]]; then
    echo "Unable to discover a built Next.js asset from the homepage." >&2
    exit 1
fi

live_css_count=0
live_js_count=0

while IFS= read -r static_asset_path; do
    [[ -z "${static_asset_path}" ]] && continue

    static_asset_type="$(curl --fail --silent --show-error --compressed \
        -o /dev/null -w '%{content_type}' \
        "https://safetech.ge${static_asset_path}")"

    case "${static_asset_path}" in
        *.js)
            [[ "${static_asset_type}" == *javascript* ]] || {
                echo "Invalid JavaScript MIME type (${static_asset_type}): ${static_asset_path}" >&2
                exit 1
            }
            live_js_count=$((live_js_count + 1))
            ;;
        *.css)
            [[ "${static_asset_type}" == text/css* ]] || {
                echo "Invalid CSS MIME type (${static_asset_type}): ${static_asset_path}" >&2
                exit 1
            }
            live_css_count=$((live_css_count + 1))
            ;;
    esac
done <<< "${static_asset_paths}"

if [[ "${live_css_count}" -eq 0 || "${live_js_count}" -eq 0 ]]; then
    echo "Homepage is missing required live static assets (css=${live_css_count}, js=${live_js_count})." >&2
    exit 1
fi

# shellcheck disable=SC2016
if ! sitemap_urls="$(php -r '
    libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $xml = stream_get_contents(STDIN);

    if (! $document->loadXML($xml) || $document->documentElement?->localName !== "sitemapindex") {
        exit(1);
    }

    $xpath = new DOMXPath($document);

    foreach ($xpath->query("//*[local-name()=\"sitemap\"]/*[local-name()=\"loc\"]") as $location) {
        echo trim($location->textContent), PHP_EOL;
    }
' <<< "${sitemap_index}")" || [[ -z "${sitemap_urls}" ]]; then
    echo "Invalid or empty sitemap index." >&2
    exit 1
fi

sitemap_page_urls=""

while IFS= read -r sitemap_url; do
    [[ -z "${sitemap_url}" ]] && continue
    child_sitemap="$(curl --fail --silent --show-error --retry 3 --retry-delay 1 \
        "${sitemap_url}")"

    # shellcheck disable=SC2016
    if ! child_page_urls="$(php -r '
        libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $xml = stream_get_contents(STDIN);

        if (! $document->loadXML($xml) || $document->documentElement?->localName !== "urlset") {
            exit(1);
        }

        $xpath = new DOMXPath($document);

        foreach ($xpath->query("/*[local-name()=\"urlset\"]/*[local-name()=\"url\"]/*[local-name()=\"loc\"]") as $location) {
            $url = trim($location->textContent);
            $parts = parse_url($url);

            if (
                ! is_array($parts)
                || ($parts["scheme"] ?? null) !== "https"
                || ($parts["host"] ?? null) !== "safetech.ge"
                || isset($parts["user"])
                || isset($parts["pass"])
                || isset($parts["port"])
                || isset($parts["fragment"])
            ) {
                exit(1);
            }

            echo $url, PHP_EOL;
        }
    ' <<< "${child_sitemap}")"; then
        echo "Invalid child sitemap: ${sitemap_url}" >&2
        exit 1
    fi

    if [[ -z "${child_page_urls}" ]]; then
        echo "Sitemap index references an empty child sitemap: ${sitemap_url}" >&2
        exit 1
    fi

    sitemap_page_urls+=$'\n'"${child_page_urls}"
done <<< "${sitemap_urls}"

sitemap_page_urls="$(printf '%s\n' "${sitemap_page_urls}" \
    | sed '/^[[:space:]]*$/d' \
    | sort -u)"

if [[ -z "${sitemap_page_urls}" ]]; then
    echo "Sitemap does not contain any indexable page URLs." >&2
    exit 1
fi

while IFS= read -r page_url; do
    [[ -z "${page_url}" ]] && continue

    if ! page_status="$(curl --silent --show-error --compressed \
        --retry 3 --retry-delay 1 --max-time 30 \
        -o /dev/null -w '%{http_code}' \
        "${page_url}")"; then
        echo "Unable to load sitemap page: ${page_url}" >&2
        exit 1
    fi

    if [[ "${page_status}" != "200" ]]; then
        echo "Sitemap page returned HTTP ${page_status}: ${page_url}" >&2
        exit 1
    fi
done <<< "${sitemap_page_urls}"

robots_content="$(curl --fail --silent --show-error --retry 3 --retry-delay 1 \
    "https://safetech.ge/robots.txt")"

if ! grep -Fq 'Sitemap: https://safetech.ge/sitemap.xml' <<< "${robots_content}"; then
    echo "robots.txt does not reference the production sitemap." >&2
    exit 1
fi

api_robots_content="$(curl --fail --silent --show-error --retry 3 --retry-delay 1 \
    "https://api.safetech.ge/robots.txt")"

if ! grep -Eq '^Disallow:[[:space:]]*/[[:space:]]*$' <<< "${api_robots_content}"; then
    echo "API robots.txt does not block crawler access." >&2
    exit 1
fi

api_robots_header="$(curl --fail --silent --show-error \
    -D - -o /dev/null \
    'https://api.safetech.ge/api/health' \
    | awk 'tolower($1) == "x-robots-tag:" { $1=""; sub(/^[[:space:]]+/, ""); print tolower($0) }' \
    | tr -d '\r' \
    | tail -n 1)"

if [[ "${api_robots_header}" != *noindex* ]]; then
    echo "API responses are missing the X-Robots-Tag noindex policy." >&2
    exit 1
fi

manifest_json="$(curl --fail --silent --show-error --retry 3 --retry-delay 1 \
    "https://safetech.ge/manifest.webmanifest")"

# shellcheck disable=SC2016
if ! php -r '
    $manifest = json_decode(stream_get_contents(STDIN), true, 512, JSON_THROW_ON_ERROR);
    exit(isset($manifest["name"], $manifest["start_url"]) ? 0 : 1);
' <<< "${manifest_json}"; then
    echo "Invalid web app manifest." >&2
    exit 1
fi

for missing_path in \
    "/services/deployment-qa-missing" \
    "/projects/deployment-qa-missing" \
    "/blog/deployment-qa-missing"; do
    missing_status="$(curl --silent --show-error -o /dev/null -w '%{http_code}' \
        "https://safetech.ge${missing_path}")"

    if [[ "${missing_status}" != "404" ]]; then
        echo "Invalid missing-content response (${missing_status}): ${missing_path}" >&2
        exit 1
    fi

    missing_html="$(curl --silent --show-error --compressed \
        -H 'Accept: text/html' \
        "https://safetech.ge${missing_path}")"
    missing_robots_count="$(grep -Eo '<meta name="robots" content="[^"]+"' <<< "${missing_html}" \
        | awk 'END { print NR + 0 }')"

    if [[ "${missing_robots_count}" -ne 1 ]] \
        || ! grep -Eq '<meta name="robots" content="[^"]*noindex' <<< "${missing_html}"; then
        echo "Missing-content page must contain exactly one noindex robots tag: ${missing_path}" >&2
        exit 1
    fi
done

invalid_lead_status="$(curl --silent --show-error -o /dev/null -w '%{http_code}' \
    -X POST \
    -H 'Content-Type: application/json' \
    --data '{}' \
    'https://safetech.ge/api/contact-leads')"

if [[ "${invalid_lead_status}" != "422" ]]; then
    echo "Contact validation endpoint returned HTTP ${invalid_lead_status}." >&2
    exit 1
fi

frontend_headers="$(curl --fail --silent --show-error --http2 \
    -D - -o /dev/null \
    -H 'Accept: text/html' \
    'https://safetech.ge/')"

for required_header in \
    'content-security-policy' \
    'strict-transport-security' \
    'x-content-type-options' \
    'referrer-policy'; do
    if ! grep -Eqi "^${required_header}:" <<< "${frontend_headers}"; then
        echo "Missing security header: ${required_header}" >&2
        exit 1
    fi
done

llms_content="$(curl --fail --silent --show-error --retry 3 --retry-delay 1 \
    "https://safetech.ge/llms.txt")"

if [[ "${llms_content}" != "# SafeTech"* ]]; then
    echo "Invalid llms.txt response." >&2
    exit 1
fi

frontend_http_version="$(curl --fail --silent --show-error --http2 \
    -o /dev/null -w '%{http_version}' \
    -H "Accept: text/html" \
    "https://safetech.ge/")"

if [[ ! "${frontend_http_version}" =~ ^(2|2\.0|3)$ ]]; then
    echo "Modern HTTP negotiation failed; received HTTP/${frontend_http_version}." >&2
    exit 1
fi

frontend_cache_status="$(curl --fail --silent --show-error --compressed \
    -D - -o /dev/null \
    -H "Accept: text/html" \
    "https://safetech.ge/" \
    | awk 'tolower($1) == "x-safetech-cache:" { print toupper($2) }' \
    | tr -d '\r' \
    | tail -n 1)"

if [[ ! "${frontend_cache_status}" =~ ^(HIT|STALE|UPDATING|REVALIDATED)$ ]]; then
    echo "Frontend HTML microcache is not active; status: ${frontend_cache_status:-missing}." >&2
    exit 1
fi

cors_origin="$(curl --fail --silent --show-error \
    -X OPTIONS \
    -H 'Origin: https://safetech.ge' \
    -H 'Access-Control-Request-Method: POST' \
    -D - -o /dev/null \
    'https://api.safetech.ge/api/contact-leads' \
    | awk 'tolower($1) == "access-control-allow-origin:" { print $2 }' \
    | tr -d '\r' \
    | tail -n 1)"

if [[ "${cors_origin}" != "https://safetech.ge" ]]; then
    echo "Production CORS validation failed; origin: ${cors_origin:-missing}." >&2
    exit 1
fi

finalize_frontend_release

echo "SafeTech deployment completed: $(git -C "${SOURCE_DIR}" rev-parse --short HEAD)"
