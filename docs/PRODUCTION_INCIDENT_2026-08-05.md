# Production frontend HTTP 500 — 2026-08-05

## Observed state

- `https://safetech.ge/` and `/about` returned HTTP 500 with the plain body `Internal Server Error`.
- The response contained Next.js headers, proving the error originated in the frontend runtime rather than a generic Nginx error page.
- `https://api.safetech.ge/api/health`, `/api/services`, and `/api/projects` returned HTTP 200.
- A clean build of the same repository commit, started against the live production CMS API, returned HTTP 200.

## Root cause

The previous deployment script removed the live frontend `.next` directory and rebuilt in the same working directory while the existing Next.js process was still serving requests. A failed or interrupted build/restart could therefore leave the active process without a complete build, causing persistent HTTP 500 responses.

The previous deployment also did not install the repository's corrected systemd and Nginx definitions on every release, allowing checked-in and installed production paths to diverge.

## Permanent correction

The replacement deployment builds in an isolated staging directory, validates the complete build before stopping the service, atomically swaps release artifacts, verifies local HTTP 200, and restores the previous release automatically if activation or later production checks fail. It also installs and validates current systemd and Nginx configuration on every deployment.
