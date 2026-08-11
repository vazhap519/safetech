import { readFileSync, readdirSync } from "node:fs";
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
const frontendSourceRoot = resolve(frontendRoot, "src");

const contentFunction = adminOptionsSource.match(
    /public static function content\(\): array\s*\{\s*return \[(.*?)\];\s*\}/s,
);
const frontendCatalog = frontendIconSource.match(
    /export const CONTENT_ICON_NAMES = \[(.*?)\] as const;/s,
);
const canonicalIconCatalog = frontendIconSource.match(
    /const iconNodes = \{(.*?)\}\s+satisfies Record<string, ReactNode>;/s,
);
const legacyAliasCatalog = frontendIconSource.match(
    /const legacyIconAliases: Record<string, CanonicalIconName> = \{(.*?)\};/s,
);

if (!contentFunction) {
    throw new Error("Could not read AdminIconOptions::content().");
}

if (!frontendCatalog) {
    throw new Error("Could not read CONTENT_ICON_NAMES from Icon.tsx.");
}

if (!canonicalIconCatalog || !legacyAliasCatalog) {
    throw new Error("Could not read the frontend icon implementation catalogs.");
}

const adminNames = [...contentFunction[1].matchAll(/'([^']+)'\s*=>/g)].map(
    ([, name]) => name,
);
const frontendNames = [...frontendCatalog[1].matchAll(/"([^"]+)"/g)].map(
    ([, name]) => name,
);
const canonicalNames = [
    ...canonicalIconCatalog[1].matchAll(/^\s+"([^"]+)"\s*:/gm),
].map(([, name]) => name);
const legacyAliasNames = [
    ...legacyAliasCatalog[1].matchAll(/^\s+([A-Za-z0-9_]+)\s*:/gm),
].map(([, name]) => name);

function sourceFiles(directory) {
    return readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
        const path = resolve(directory, entry.name);

        if (entry.isDirectory()) return sourceFiles(path);

        return /\.(ts|tsx)$/.test(entry.name) ? [path] : [];
    });
}

const hardcodedIconNames = sourceFiles(frontendSourceRoot).flatMap((path) => {
    const source = readFileSync(path, "utf8");
    const componentNames = [
        ...source.matchAll(/<Icon\b[\s\S]{0,400}?\bname="([^"]+)"/g),
    ].map(([, name]) => name);
    const itemNames = [...source.matchAll(/\bicon:\s*"([^"]+)"/g)].map(
        ([, name]) => name,
    );

    return [...componentNames, ...itemNames];
});

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

const supportedNames = new Set([
    ...frontendNames,
    ...canonicalNames,
    ...legacyAliasNames,
]);
const unresolvedHardcodedNames = [
    ...new Set(hardcodedIconNames.filter((name) => !supportedNames.has(name))),
];

if (unresolvedHardcodedNames.length) {
    throw new Error(
        `Hardcoded frontend icons are not implemented: ${unresolvedHardcodedNames.join(", ")}.`,
    );
}

console.log(
    `Icon catalog check passed (${adminNames.length} selectable, ${new Set(hardcodedIconNames).size} hardcoded icons).`,
);
