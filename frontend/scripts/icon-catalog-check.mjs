import { readFileSync } from "node:fs";
import { dirname, resolve } from "node:path";
import { fileURLToPath } from "node:url";

const scriptDirectory = dirname(fileURLToPath(import.meta.url));
const frontendRoot = resolve(scriptDirectory, "..");
const repositoryRoot = resolve(frontendRoot, "..");

const adminOptionsSource = readFileSync(
    resolve(
        repositoryRoot,
        "back/app/Filament/Support/AdminIconOptions.php",
    ),
    "utf8",
);
const frontendIconSource = readFileSync(
    resolve(frontendRoot, "src/components/ui/Icon.tsx"),
    "utf8",
);

const contentFunction = adminOptionsSource.match(
    /public static function content\(\): array\s*\{\s*return \[(.*?)\];\s*\}/s,
);
const frontendCatalog = frontendIconSource.match(
    /export const CONTENT_ICON_NAMES = \[(.*?)\] as const;/s,
);

if (!contentFunction) {
    throw new Error("Could not read AdminIconOptions::content().");
}

if (!frontendCatalog) {
    throw new Error("Could not read CONTENT_ICON_NAMES from Icon.tsx.");
}

const adminNames = [...contentFunction[1].matchAll(/'([^']+)'\s*=>/g)].map(
    ([, name]) => name,
);
const frontendNames = [...frontendCatalog[1].matchAll(/"([^"]+)"/g)].map(
    ([, name]) => name,
);

function duplicates(values) {
    return values.filter((value, index) => values.indexOf(value) !== index);
}

const duplicateAdminNames = [...new Set(duplicates(adminNames))];
const duplicateFrontendNames = [...new Set(duplicates(frontendNames))];

if (duplicateAdminNames.length || duplicateFrontendNames.length) {
    throw new Error(
        `Duplicate selectable icons found. Admin: ${duplicateAdminNames.join(", ") || "none"}; frontend: ${duplicateFrontendNames.join(", ") || "none"}.`,
    );
}

const missingOnFrontend = adminNames.filter(
    (name) => !frontendNames.includes(name),
);
const missingInAdmin = frontendNames.filter((name) => !adminNames.includes(name));

if (missingOnFrontend.length || missingInAdmin.length) {
    throw new Error(
        [
            `Selectable icon catalogs are out of sync.`,
            `Missing on frontend: ${missingOnFrontend.join(", ") || "none"}.`,
            `Missing in admin: ${missingInAdmin.join(", ") || "none"}.`,
        ].join(" "),
    );
}

console.log(`Selectable icon catalog check passed (${adminNames.length} icons).`);
