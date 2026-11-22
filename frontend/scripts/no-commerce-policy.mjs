import { readFileSync, readdirSync, statSync } from "node:fs";
import { join, relative } from "node:path";

const root = new URL("..", import.meta.url).pathname.replace(/^\/(\w:)/, "$1");
const packageJson = JSON.parse(readFileSync(join(root, "package.json"), "utf8"));
const forbiddenPackages = [
    "stripe", "@stripe/stripe-js", "@stripe/react-stripe-js",
    "paypal-rest-sdk", "@paypal/react-paypal-js", "shopify-api-node",
    "@shopify/shopify-api", "woocommerce",
];
const dependencies = { ...packageJson.dependencies, ...packageJson.devDependencies };
const installedCommercePackages = forbiddenPackages.filter((name) => name in dependencies);

const forbiddenRouteSegments = new Set([
    "cart", "checkout", "shop", "store", "basket", "payment", "payments",
]);
const forbiddenPublicPhrases = [
    /\bbuy now\b/i,
    /\badd to cart\b/i,
    /\bpay now\b/i,
    /კალათაში დამატება/u,
    /ახლავე ყიდვა/u,
    /купить сейчас/iu,
    /добавить в корзину/iu,
];
const textExtensions = new Set([".ts", ".tsx", ".js", ".jsx"]);
const violations = [];

function extension(path) {
    const dot = path.lastIndexOf(".");
    return dot === -1 ? "" : path.slice(dot);
}

function walk(directory) {
    for (const entry of readdirSync(directory)) {
        const path = join(directory, entry);
        const info = statSync(path);
        if (info.isDirectory()) {
            walk(path);
            continue;
        }
        if (!textExtensions.has(extension(path))) continue;

        const source = readFileSync(path, "utf8");
        for (const phrase of forbiddenPublicPhrases) {
            if (phrase.test(source)) {
                violations.push(`${relative(root, path)} contains public commerce copy: ${phrase}`);
            }
        }
    }
}

const appRoot = join(root, "src", "app");
for (const entry of readdirSync(appRoot, { recursive: true, withFileTypes: true })) {
    if (!entry.isDirectory()) continue;
    const segment = entry.name.toLowerCase().replace(/^\(.+\)$/, "");
    if (forbiddenRouteSegments.has(segment)) {
        violations.push(`src/app contains forbidden commerce route segment: ${segment}`);
    }
}

walk(join(root, "src"));

if (installedCommercePackages.length) {
    violations.push(`commerce dependencies are forbidden: ${installedCommercePackages.join(", ")}`);
}

if (violations.length) {
    console.error("No-commerce policy failed:\n" + violations.map((item) => `- ${item}`).join("\n"));
    process.exit(1);
}

console.log("No-commerce policy passed: the public site has no cart, checkout, payment routes, or purchase CTAs.");
