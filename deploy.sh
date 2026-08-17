#!/usr/bin/env bash

set -Eeuo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
API_URL="${SAFETECH_API_URL:-https://api.safetech.ge}"
REMOTE="${SAFETECH_REMOTE:-origin}"
BRANCH="${SAFETECH_BRANCH:-main}"
DEPLOY_SCRIPT="${SCRIPT_DIR}/scripts/deploy-production.sh"
PATCHED_DEPLOY="$(mktemp /tmp/safetech-deploy-production.XXXXXX.sh)"
DEPLOY_STATE_DIR="${SAFETECH_DEPLOY_STATE_DIR:-/var/lib/safetech}"
FRONTEND_RELEASE_MARKER="${DEPLOY_STATE_DIR}/frontend-release-commit"
SKIP_FRONTEND_BUILD=0

cleanup_patched_deploy() {
    rm -f "${PATCHED_DEPLOY}"
}
trap cleanup_patched_deploy EXIT INT TERM

bash "${SCRIPT_DIR}/scripts/repair-backend-runtime.sh"

# Fetch first so we can compare the next release against the last frontend that
# was actually deployed successfully. Comparing only the current checkout to
# origin/main is not sufficient after a failed frontend build because Git may
# already be at the new commit while the live .next release is still old.
git -C "${SCRIPT_DIR}" fetch --prune "${REMOTE}"
TARGET_REF="${REMOTE}/${BRANCH}"

install -d -o root -g root -m 0755 "${DEPLOY_STATE_DIR}"

if [[ -s "${FRONTEND_RELEASE_MARKER}" ]]; then
    LAST_FRONTEND_RELEASE="$(tr -d '[:space:]' < "${FRONTEND_RELEASE_MARKER}")"

    if git -C "${SCRIPT_DIR}" cat-file -e "${LAST_FRONTEND_RELEASE}^{commit}" 2>/dev/null; then
        if git -C "${SCRIPT_DIR}" diff --quiet \
            "${LAST_FRONTEND_RELEASE}" "${TARGET_REF}" -- frontend/; then
            SKIP_FRONTEND_BUILD=1
            printf 'No frontend changes since deployed release %s; skipping Next.js rebuild.\n' \
                "${LAST_FRONTEND_RELEASE:0:12}"
        else
            printf 'Frontend changes detected since deployed release %s; Next.js rebuild required.\n' \
                "${LAST_FRONTEND_RELEASE:0:12}"
        fi
    else
        printf 'Frontend release marker is not a valid local commit; rebuilding frontend safely.\n'
    fi
else
    printf 'No frontend release marker exists yet; rebuilding frontend safely.\n'
fi

# Production deploy already performs a strict TypeScript check and a full Next.js
# production build. ESLint has repeatedly stalled on this small production host,
# so lint remains a CI/development concern. For backend-only releases, also omit
# the expensive isolated Next.js build and activation block entirely. Public
# frontend smoke checks still run against the currently active release.
awk -v skip_frontend="${SKIP_FRONTEND_BUILD}" '
    /log "Linting staged frontend"/ { skipping_lint = 1; next }
    /log "Type-checking staged frontend"/ { skipping_lint = 0 }

    skip_frontend == "1" && /log "Preparing isolated frontend build"/ {
        skipping_frontend = 1
        next
    }
    skip_frontend == "1" && /log "Restarting the queue worker"/ {
        skipping_frontend = 0
    }

    !skipping_lint && !skipping_frontend { print }
' "${DEPLOY_SCRIPT}" > "${PATCHED_DEPLOY}"
chmod 0700 "${PATCHED_DEPLOY}"

SAFETECH_PROJECT_DIR="${SAFETECH_PROJECT_DIR:-${SCRIPT_DIR}}" \
    bash "${PATCHED_DEPLOY}" "$@"

# Reaching this point means the production deploy and all of its smoke checks
# succeeded. Advancing the marker after backend-only releases is safe because we
# only skip when there were no frontend changes between the previous marker and
# the target release.
DEPLOYED_COMMIT="$(git -C "${SCRIPT_DIR}" rev-parse HEAD)"
printf '%s\n' "${DEPLOYED_COMMIT}" > "${FRONTEND_RELEASE_MARKER}"
chmod 0644 "${FRONTEND_RELEASE_MARKER}"
printf 'Frontend release marker updated to %s.\n' "${DEPLOYED_COMMIT:0:12}"

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
