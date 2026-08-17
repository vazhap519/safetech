import { defineConfig, globalIgnores } from "eslint/config";
import nextVitals from "eslint-config-next/core-web-vitals";
import nextTs from "eslint-config-next/typescript";

const eslintConfig = defineConfig([
  ...nextVitals,
  ...nextTs,
  globalIgnores([
    ".next/**",
    "out/**",
    "build/**",
    "dist/**",
    "coverage/**",
    "node_modules/**",
    "public/**",
    "scripts/**",
    "deploy/**",
    "Back/**",
    "next-env.d.ts",
    "next.config.*",
    "postcss.config.*",
    "tailwind.config.*",
  ]),
]);

export default eslintConfig;
