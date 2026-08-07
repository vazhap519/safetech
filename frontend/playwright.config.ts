import { defineConfig, devices } from "@playwright/test";

const baseURL = process.env.E2E_BASE_URL || "http://127.0.0.1:3000";

export default defineConfig({
    testDir: "./e2e",
    timeout: 45_000,
    expect: {
        timeout: 10_000,
    },
    fullyParallel: false,
    retries: process.env.CI ? 1 : 0,
    workers: process.env.CI ? 1 : undefined,
    reporter: process.env.CI
        ? [["line"], ["html", { outputFolder: "playwright-report", open: "never" }]]
        : "list",
    use: {
        baseURL,
        trace: "retain-on-failure",
        screenshot: "only-on-failure",
        video: "retain-on-failure",
    },
    projects: [
        {
            name: "desktop",
            use: {
                ...devices["Desktop Chrome"],
                viewport: { width: 1440, height: 1100 },
            },
        },
        {
            name: "mobile",
            use: {
                ...devices["Pixel 7"],
            },
        },
    ],
});
