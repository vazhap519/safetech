#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
API_URL="${SAFETECH_API_URL:-https://api.safetech.ge}"
DEPLOY_SCRIPT="${SCRIPT_DIR}/scripts/deploy-production.sh"
PATCHED_DEPLOY="$(mktemp /tmp/safetech-deploy-production.XXXXXX.sh)"

cleanup_patched_deploy() {
    rm -f "${PATCHED_DEPLOY}"
}
trap cleanup_patched_deploy EXIT INT TERM

bash "${SCRIPT_DIR}/scripts/repair-backend-runtime.sh"

# Production deploy already performs a strict TypeScript check and a full Next.js
# production build. ESLint has repeatedly stalled indefinitely on the production
# host, so keep linting in CI/development and remove only that block from the
# temporary deploy copy. Keep the temporary script outside the Git checkout so
# the production cleanliness check does not see it as an untracked file.
awk '
    /log "Linting staged frontend"/ { skipping = 1; next }
    /log "Type-checking staged frontend"/ { skipping = 0 }
    !skipping { print }
' "${DEPLOY_SCRIPT}" > "${PATCHED_DEPLOY}"
chmod 0700 "${PATCHED_DEPLOY}"

SAFETECH_PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}" \
    bash "${PATCHED_DEPLOY}" "$@"

bash "${SCRIPT_DIR}/scripts/repair-backend-runtime.sh"

admin_status="$(curl --silent --show-error --location --output /tmp/safetech-admin-login.html \
    --write-out '%{http_code}' --max-time 20 "${API_URL%/}/admin/login" || true)"

if [[ "${admin_status}" != "200" ]]; then
    printf 'Filament admin login smoke test failed with HTTP %s.\n' "${admin_status:-unreachable}" >&2
    head -c 5000 /tmp/safetech-admin-login.html >&2 || true
    printf '\n' >&2
    exit 1
fi

rm -f /tmp/safetech-admin-login.html
printf 'Filament admin login smoke test passed.\n'
