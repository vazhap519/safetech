# SafeTech CCTV Vision Planner

The public camera planner offers an optional opt-in paid AI-assisted draft. Manual planning, JSON/SVG export, the admin engineering calculator and quote submission still work when AI is disabled.

## Enable on Laravel API server

In the **private** API environment (back/.env; never commit secrets), set:

    OPENAI_API_KEY=<server-side OpenAI credential>
    AI_CAMERA_PLANNER_ENABLED=true
    OPENAI_PLANNER_MODEL=gpt-4.1-mini

Then clear or rebuild Laravel's cached config. The public Laravel endpoint is POST /api/camera-plans/vision; the Next.js same-origin proxy uses POST /api/camera-plans/vision. Never place an OpenAI key in NEXT_PUBLIC_ variables or browser requests.

The AI planner is **disabled by default** to avoid unexpected API charges. Its rate limit is one request/minute and three/hour per IP plus ten requests/day globally. Production should use shared Redis/database-backed cache, not per-process array cache. Check the reverse proxy client-IP setup and Nginx/PHP maximum body size (image maximum 5 MiB). The analysis endpoint does not persist images; the provider request specifies store=false. Users must separately opt in before images leave their browser.

## Accuracy, review and handoff

1. Upload a legible floor/site plan, PNG/JPEG/WebP. A perspective photo cannot reliably reveal room polygon, physical scale or hidden walls.
2. Enter overall image width in meters; check scale-confirmed only when you physically measured it.
3. Grant image-processing consent and request AI suggestions.
4. Review proposed walls, room labels, inspection area, camera count and position, reasons and cautions. Preview does not replace the manual plan.
5. Explicitly Apply or Discard. Applying existing geometry requires confirmation; Undo restores it. Verify onsite FOV, occlusions, mounting height, lighting, manufacturer range, neighbors' privacy, cable/PoE and recording retention.

The AI uses source-image coordinates normalized 0..1000 and the client maps those into 900×600 Canvas through the actual image contain transform. JSON is schema-constrained and validated again in Laravel. If AI is unavailable or offers no defensible camera positions, use the manual planner. Results are approximate proposals, not security compliance or a final technical quotation.

## Production deployment prerequisite

GitHub's QA-gated Deploy Production workflow requires SSH credentials stored in GitHub Actions secrets SAFETECH_DEPLOY_SSH_KEY and SAFETECH_DEPLOY_KNOWN_HOSTS. These are not the OpenAI credential. Verify the host's fingerprint out of band; do not paste private keys in chat, logs or commits. Once configured, rerun the deploy of the intended main SHA and verify API /api/health reports that commit.
