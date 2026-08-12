#!/usr/bin/env bash

set -Eeuo pipefail

DEPLOY_USER="${SAFETECH_DEPLOY_USER:-safetech-deploy}"
PROJECT_DIR="${SAFETECH_PROJECT_DIR:-/var/www/safetech}"
DEPLOY_HOST="${SAFETECH_DEPLOY_HOST:-95.179.163.147}"
PRIVATE_KEY="${SAFETECH_BOOTSTRAP_PRIVATE_KEY:-/root/.safetech-github-actions-deploy}"
AUTHORIZED_KEYS="/home/${DEPLOY_USER}/.ssh/authorized_keys"
SSH_GATE="/usr/local/sbin/safetech-deploy-ssh-gate"
DEPLOY_WRAPPER="/usr/local/sbin/safetech-deploy-commit"
SUDOERS_FILE="/etc/sudoers.d/safetech-deploy"
APPROVED_REMOTE_DIR="/var/lib/safetech-approved.git"
APPROVED_REMOTE_NAME="safetech-approved"
APPROVED_BRANCH="deploy-approved"

fail() {
    printf 'Bootstrap failed: %s\n' "$*" >&2
    exit 1
}

[[ "${EUID}" -eq 0 ]] || fail "run as root"
[[ -d "${PROJECT_DIR}/.git" ]] || fail "SafeTech checkout not found at ${PROJECT_DIR}"
command -v ssh-keygen >/dev/null 2>&1 || fail "ssh-keygen is required"
command -v visudo >/dev/null 2>&1 || fail "visudo is required"
command -v git >/dev/null 2>&1 || fail "git is required"

if ! id "${DEPLOY_USER}" >/dev/null 2>&1; then
    useradd --create-home --shell /bin/bash "${DEPLOY_USER}"
fi

install -d -o "${DEPLOY_USER}" -g "${DEPLOY_USER}" -m 0700 "/home/${DEPLOY_USER}/.ssh"
touch "${AUTHORIZED_KEYS}"
chown "${DEPLOY_USER}:${DEPLOY_USER}" "${AUTHORIZED_KEYS}"
chmod 0600 "${AUTHORIZED_KEYS}"

if [[ ! -f "${PRIVATE_KEY}" ]]; then
    ssh-keygen -q -t ed25519 -N '' -C 'safetech-github-actions-production' -f "${PRIVATE_KEY}"
fi
chmod 0600 "${PRIVATE_KEY}"

public_key="$(cat "${PRIVATE_KEY}.pub")"
authorized_line="restrict,command=\"${SSH_GATE}\" ${public_key}"

tmp_authorized="$(mktemp)"
grep -v 'safetech-github-actions-production' "${AUTHORIZED_KEYS}" > "${tmp_authorized}" || true
printf '%s\n' "${authorized_line}" >> "${tmp_authorized}"
install -o "${DEPLOY_USER}" -g "${DEPLOY_USER}" -m 0600 "${tmp_authorized}" "${AUTHORIZED_KEYS}"
rm -f "${tmp_authorized}"

cat > "${SSH_GATE}" <<'GATE'
#!/usr/bin/env bash
set -Eeuo pipefail

original="${SSH_ORIGINAL_COMMAND:-}"

if [[ ! "${original}" =~ ^deploy[[:space:]]+([0-9a-f]{40})$ ]]; then
    printf 'Only an exact deploy <40-char-sha> command is allowed.\n' >&2
    exit 64
fi

exec sudo -n /usr/local/sbin/safetech-deploy-commit "${BASH_REMATCH[1]}"
GATE
chown root:root "${SSH_GATE}"
chmod 0755 "${SSH_GATE}"

cat > "${DEPLOY_WRAPPER}" <<WRAPPER
#!/usr/bin/env bash
set -Eeuo pipefail

PROJECT_DIR="${PROJECT_DIR}"
APPROVED_REMOTE_DIR="${APPROVED_REMOTE_DIR}"
APPROVED_REMOTE_NAME="${APPROVED_REMOTE_NAME}"
APPROVED_BRANCH="${APPROVED_BRANCH}"

fail() {
    printf 'Verified deploy failed: %s\\n' "\$*" >&2
    exit 1
}

[[ "\${EUID}" -eq 0 ]] || fail "must run as root"
[[ "\$#" -eq 1 ]] || fail "exactly one commit SHA is required"
sha="\$1"
[[ "\${sha}" =~ ^[0-9a-f]{40}$ ]] || fail "invalid commit SHA"
[[ -d "\${PROJECT_DIR}/.git" ]] || fail "production checkout is missing"

if [[ -n "\$(git -C "\${PROJECT_DIR}" status --porcelain)" ]]; then
    git -C "\${PROJECT_DIR}" status --short >&2
    fail "production checkout contains uncommitted changes"
fi

git -C "\${PROJECT_DIR}" fetch --prune origin main
main_sha="\$(git -C "\${PROJECT_DIR}" rev-parse refs/remotes/origin/main)"
[[ "\${main_sha}" == "\${sha}" ]] || fail "requested SHA is not the current origin/main commit"

git -C "\${PROJECT_DIR}" cat-file -e "\${sha}^{commit}" 2>/dev/null || fail "requested commit is unavailable"

if [[ ! -d "\${APPROVED_REMOTE_DIR}" ]]; then
    git init --bare "\${APPROVED_REMOTE_DIR}" >/dev/null
fi

git -C "\${PROJECT_DIR}" push --force "\${APPROVED_REMOTE_DIR}" \
    "\${sha}:refs/heads/\${APPROVED_BRANCH}" >/dev/null

if git -C "\${PROJECT_DIR}" remote get-url "\${APPROVED_REMOTE_NAME}" >/dev/null 2>&1; then
    git -C "\${PROJECT_DIR}" remote set-url "\${APPROVED_REMOTE_NAME}" "\${APPROVED_REMOTE_DIR}"
else
    git -C "\${PROJECT_DIR}" remote add "\${APPROVED_REMOTE_NAME}" "\${APPROVED_REMOTE_DIR}"
fi

git -C "\${PROJECT_DIR}" fetch "\${APPROVED_REMOTE_NAME}" \
    "+refs/heads/\${APPROVED_BRANCH}:refs/remotes/\${APPROVED_REMOTE_NAME}/\${APPROVED_BRANCH}" >/dev/null

git -C "\${PROJECT_DIR}" checkout -B "\${APPROVED_BRANCH}" \
    "\${APPROVED_REMOTE_NAME}/\${APPROVED_BRANCH}" >/dev/null

current_sha="\$(git -C "\${PROJECT_DIR}" rev-parse HEAD)"
[[ "\${current_sha}" == "\${sha}" ]] || fail "failed to pin production checkout to requested SHA"

SAFETECH_REMOTE="\${APPROVED_REMOTE_NAME}" \
SAFETECH_BRANCH="\${APPROVED_BRANCH}" \
bash "\${PROJECT_DIR}/deploy.sh"

final_sha="\$(git -C "\${PROJECT_DIR}" rev-parse HEAD)"
[[ "\${final_sha}" == "\${sha}" ]] || fail "deployed checkout moved away from approved SHA"
WRAPPER
chown root:root "${DEPLOY_WRAPPER}"
chmod 0755 "${DEPLOY_WRAPPER}"

cat > "${SUDOERS_FILE}" <<SUDOERS
${DEPLOY_USER} ALL=(root) NOPASSWD: ${DEPLOY_WRAPPER} *
SUDOERS
chown root:root "${SUDOERS_FILE}"
chmod 0440 "${SUDOERS_FILE}"
visudo -cf "${SUDOERS_FILE}" >/dev/null

if [[ ! -f /etc/ssh/ssh_host_ed25519_key.pub ]]; then
    fail "server Ed25519 SSH host key is missing"
fi

host_key="$(awk '{print $1 " " $2}' /etc/ssh/ssh_host_ed25519_key.pub)"

printf '\n=== GitHub Actions setup values ===\n\n'
printf 'Create repository Actions secret: SAFETECH_DEPLOY_SSH_KEY\n'
printf '%s\n' '----- copy everything between the markers -----'
cat "${PRIVATE_KEY}"
printf '%s\n' '----- end private key -----'
printf '\nCreate repository Actions secret: SAFETECH_DEPLOY_KNOWN_HOSTS\n'
printf '%s %s\n' "${DEPLOY_HOST}" "${host_key}"
printf '\nAfter both secrets are saved in GitHub, delete the temporary private key from this server:\n'
printf 'rm -f %q %q\n' "${PRIVATE_KEY}" "${PRIVATE_KEY}.pub"
printf '\nThe authorized public key remains installed for the restricted %s account.\n' "${DEPLOY_USER}"
printf 'SSH access from this key is restricted to deploying a QA-approved 40-character commit SHA.\n'
