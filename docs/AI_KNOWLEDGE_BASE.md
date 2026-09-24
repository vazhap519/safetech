# SafeTech AI Knowledge Base — curated seed

## Content and purpose

92 original technical/customer-support topics in Georgian, English and Russian (276 approved records), in addition to existing manually managed AI knowledge. Topics cover CCTV, IP/PoE NVR/DVR, storage, UPS, 4G solar cameras, troubleshooting, LAN/structured CAT6 cabling, MikroTik, Wi-Fi, VPN/VLAN, door access, RFID, intercoms, elevator integration, barrier gates/LPR, Windows, business IT support, Microsoft 365, POS and the lead-qualification process. Guidance includes critical caveats: no unverified prices or stock, no fixed UPS autonomy without calculation, no fabricated projects, no dangerous lock/mains/elevator wiring without a qualified technician, and no asking for passwords.

Each article is separately keyed and written in all three languages, not machine translated at request time. Article identifiers are stable source references prefixed safetech-kb:v1:, and source type is curated. New entries are inserted only. The seeder never overwrites title, content, status, usage_count or source_reference on an existing record.

## Deployment and manual seed

Normal production deploy invokes SystemContentSeeder, which now calls AiKnowledgeBaseSeeder. When the new backend code is deployed, it will seed the data with no third-party API calls or migration changes. For a one-time manual run after pulling main:

    cd /var/www/safetech/back
    php artisan db:seed --class=AiKnowledgeBaseSeeder --force

Never run migrate:fresh in production. The seeder preserves disabled and draft records, preserves manually edited entries and respects a tombstone when a canonical record is deliberately deleted through the CMS model. Deleting an approved item will not cause it to reappear at the next deployment. To disable guidance without deleting it, change its status to disabled in SafeTech CMS > AI ცოდნა.

## How assistant retrieval works

The search_knowledge tool uses only approved items in the requested locale and the Georgian fallback. The old newest-150 cap was removed; a ranked full-corpus search selects up to five relevant items using matched words and title weighting. This is keyword-based retrieval, not embeddings, vector search or model fine-tuning. The chatbot still needs a working OpenAI API key and AI chat feature flag to respond. Adding approved knowledge does not guarantee every reply is accurate; review real chat feedback, correct articles in Filament and keep product prices/stock in verified live business data.

## Editorial requirements

A production technician or business owner should periodically review technical advice and the actual equipment model datasheets. If a fact changes, edit that article in CMS; the seeder will preserve the revised entry. Never put customer data, passwords, secret keys, fictitious customer cases, unsupported brand claims or outdated fixed quotes into this shared public assistant knowledge. Additional languages should be authored and reviewed rather than blindly replacing Georgian.
