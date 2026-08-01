# Service calculator restoration

The approximate price calculator is embedded directly on the Services page.

## Behaviour

- Loads localized calculator profiles from `GET /api/service-calculator/profiles`.
- Uses per-service calculator settings from the existing Filament **Lead form and advanced calculator** section.
- Falls back to practical default profiles when a published service has no calculator configuration yet.
- Supports Georgian, English and Russian labels.
- Calculates one-time and recurring estimates in the browser.
- Shows an itemized estimate and links to Contact for a final technical quote.
- Does not restore a separate public `/service-calculator` page.

The result is explicitly presented as indicative; final pricing is confirmed after technical assessment.
