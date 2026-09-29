# Intercom configurator

Staff: `/admin/access-intercom-configurator-page`. Public: select the intercom service in `/service-calculator` (KA/EN/RU). Choose 1–100 apartments/subscribers and 1–10 common entrance doors, then open the matching equipment selection. One subscriber is a call address, not a count of people in a household.

`IntercomProfile` supplies seeded choices, device metadata and the bill of materials to both interfaces. `IntercomPlanner` performs staff-side calculations; the public TypeScript calculator uses the same API catalog. Scope changes hide the previous bill until confirmed. Incompatible panels/monitors are replaced together, and quantities cannot be overridden into an undersized public kit.

## Seeding and prices

`SystemContentSeeder` calls `IntercomConfiguratorSeeder`; the regular production seed sequence already runs it. To apply only this upgrade to existing services:

```sh
cd back
php artisan db:seed --class=IntercomConfiguratorSeeder --force
```

The versioned upgrade retains existing prices for matching component keys, pricing settings, and custom fields/components. The old configuration is retained in `lead_form.intercom_previous_config`. Obsolete generic intercom components are archived there instead of being double-counted. Subsequent runs preserve admin changes. No TVT prices are invented: zero-priced seeded items show “Price on request”; totals containing them are labeled as priced-item subtotals. Prices can be entered in the service configurator resource.

## Device sources and design limits

- [TVT apartment and villa topology](https://www.tvt.net.cn/keyTechnologies/index1280.html): TD-E2223 + TD-E2137, up to 500 primary room stations, five extensions per room, one primary and nine secondary entrance stations. The UI intentionally supports only 100 apartments and 10 doors.
- [Apartment diagram](https://en.tvt.net.cn/file/editor/picture/3d74401318e783d5250f515c83b0a462.jpg), [villa diagram](https://en.tvt.net.cn/file/editor/picture/aecd9fe851b61af3d4b20eb559dabe94.jpg).
- [TD-E2223](https://www.tvt.net.cn/products/1386.html), [TD-E2137](https://www.tvt.net.cn/products/1388.html).
- [TE-VD1108](https://www.tvt.net.cn/products/1845.html): 1/2/4/8 button layouts. Only one entrance is offered until its multi-entrance firmware has been checked. [TE-VH1104](https://www.tvt.net.cn/products/1900.html) is offered within the TE family. TD and TE are deliberately separate catalog families; commissioning must verify exact firmware support.
- Legacy single-button Hikvision panels are offered only for one subscriber and one door, never as apartment-building panels.

These are candidate equipment configurations. Final firmware, opening modes, relay ratings, lock mechanics and model availability are checked at commissioning/procurement. RFID card capacity is never used as apartment capacity.

## Quantity rules

- Indoor monitors = apartments × monitors per apartment (1–6). One panel, lock, PSU, exit device and mounting kit per entrance. Door contacts, closers, backup batteries, SD cards, additional readers and surge protection follow the selected options. RFID tags = apartments × tags per apartment.
- All selected endpoints use wired standard PoE. A conservative **15.4 W PSE allocation per endpoint plus 20% design reserve** is used, rather than adding only device-side typical wattages. Usable switch endpoints = min(PoE ports, floor(budget / 18.48 W)). Distribution switch count rounds up. Each switch has a separate Gigabit uplink port, not a shared PoE/uplink port.
- Switch entries are minimum procurement specifications, not claimed TVT models. Automatic selection chooses the smallest sufficient single switch or the largest listed size for multiple switches. Choices that need more than 47 distribution switches are removed, allowing a 48-port aggregation switch with one reserved router port. Core sizes are 8/16/24/48. This is a star layout on one shared LAN; independent buildings/entrances require separate designs.
- UPS/cabinet count assumes one distributed cabinet per PoE switch plus a core cabinet when needed. UPS watts/runtime, battery capacity and cabinet dimensions need a survey. Lock PSUs are separate from panel relays and have at least 30% lock-current reserve plus allowances for selected reader/touchless button; individual PSU minimum is 2 A at 12 V.
- Cable and conduit lengths start at zero and are counted only when measured lengths are entered. Ethernet links beyond 100 m need a revised topology or fiber. Patch panels, terminations, patch leads and labels are included. Check supplied mounting brackets before ordering duplicates.

## Verification

PHP tests cover seeded ranges, upgrade/idempotence, price preservation, all three locales, staff step transitions, model family changes, bounds, optional accessories and capacity at representative scopes up to 100 apartments × 6 monitors + 10 doors. Browser tests exercise the actual public flow, automatic quantities, quote handoff, and the PHP/TypeScript calculation contract on desktop and mobile.
