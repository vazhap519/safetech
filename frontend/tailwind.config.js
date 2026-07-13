/** @type {import('tailwindcss').Config} */
module.exports = {
    darkMode: "class",

    content: [
        "./src/**/*.{js,ts,jsx,tsx,mdx}",
        "./app/**/*.{js,ts,jsx,tsx,mdx}",
        "./components/**/*.{js,ts,jsx,tsx,mdx}",
        "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    ],

    theme: {
        extend: {
            colors: {
                background: "#070B14",

                surface: "#11131b",
                "surface-dim": "#11131b",
                "surface-bright": "#373942",

                "surface-container-lowest": "#0c0e16",
                "surface-container-low": "#191b23",
                "surface-container": "#1d1f27",
                "surface-container-high": "#282a32",
                "surface-container-highest": "#32343d",

                "surface-variant": "#32343d",
                "surface-tint": "#b4c5ff",

                primary: "#b4c5ff",
                "primary-container": "#2563eb",
                "primary-fixed": "#dbe1ff",
                "primary-fixed-dim": "#b4c5ff",

                secondary: "#4cd7f6",
                "secondary-container": "#03b5d3",
                "secondary-fixed": "#acedff",
                "secondary-fixed-dim": "#4cd7f6",

                tertiary: "#ffb596",
                "tertiary-container": "#bc4800",
                "tertiary-fixed": "#ffdbcd",
                "tertiary-fixed-dim": "#ffb596",

                error: "#ffb4ab",
                "error-container": "#93000a",

                success: "#22c55e",
                warning: "#f59e0b",
                info: "#3b82f6",

                outline: "#8d90a0",
                "outline-variant": "#434655",

                "on-background": "#e1e2ed",
                "on-surface": "#e1e2ed",
                "on-surface-variant": "#c3c6d7",

                "on-primary": "#002a78",
                "on-primary-container": "#eeefff",
                "on-primary-fixed": "#00174b",
                "on-primary-fixed-variant": "#003ea8",

                "on-secondary": "#003640",
                "on-secondary-container": "#00424e",
                "on-secondary-fixed": "#001f26",
                "on-secondary-fixed-variant": "#004e5c",

                "on-tertiary": "#581e00",
                "on-tertiary-container": "#ffede6",
                "on-tertiary-fixed": "#360f00",
                "on-tertiary-fixed-variant": "#7d2d00",

                "on-error": "#690005",
                "on-error-container": "#ffdad6",

                "inverse-surface": "#e1e2ed",
                "inverse-on-surface": "#2e3039",
                "inverse-primary": "#0053db",
            },

            borderRadius: {
                DEFAULT: "0.25rem",
                lg: "0.5rem",
                xl: "0.75rem",
                "2xl": "1rem",
                full: "9999px",
            },

            spacing: {
                "unit-xs": "4px",
                "unit-sm": "8px",
                "unit-md": "16px",
                "unit-lg": "clamp(24px, 4vw, 32px)",
                "unit-xl": "clamp(48px, 7vw, 64px)",

                gutter: "clamp(16px, 2.5vw, 24px)",

                "margin-mobile": "20px",
                "margin-desktop": "clamp(20px, 5vw, 64px)",

                "container-max": "1440px",
            },

            maxWidth: {
                container: "1440px",
            },

            fontFamily: {
                display: ["Segoe UI", "system-ui", "sans-serif"],
                body: ["Segoe UI", "system-ui", "sans-serif"],

                "mono-sm": ["Segoe UI", "system-ui", "sans-serif"],
                "headline-md": ["Segoe UI", "system-ui", "sans-serif"],
                "headline-xl": ["Segoe UI", "system-ui", "sans-serif"],
                "headline-lg": ["Segoe UI", "system-ui", "sans-serif"],
                "display-lg": ["Segoe UI", "system-ui", "sans-serif"],

                "label-md": ["Segoe UI", "system-ui", "sans-serif"],
                "body-md": ["Segoe UI", "system-ui", "sans-serif"],
                "body-lg": ["Segoe UI", "system-ui", "sans-serif"],
            },

            fontSize: {
                "mono-sm": [
                    "12px",
                    {
                        lineHeight: "1.5",
                        letterSpacing: "0.1em",
                        fontWeight: "400",
                    },
                ],

                "label-md": [
                    "14px",
                    {
                        lineHeight: "1",
                        letterSpacing: "0.05em",
                        fontWeight: "500",
                    },
                ],

                "body-md": [
                    "16px",
                    {
                        lineHeight: "1.6",
                        fontWeight: "400",
                    },
                ],

                "body-lg": [
                    "18px",
                    {
                        lineHeight: "1.6",
                        fontWeight: "400",
                    },
                ],

                "headline-md": [
                    "24px",
                    {
                        lineHeight: "1.4",
                        fontWeight: "500",
                    },
                ],

                "headline-lg": [
                    "32px",
                    {
                        lineHeight: "1.3",
                        fontWeight: "600",
                    },
                ],

                "headline-xl": [
                    "40px",
                    {
                        lineHeight: "1.2",
                        fontWeight: "600",
                    },
                ],

                "display-lg-mobile": [
                    "34px",
                    {
                        lineHeight: "1.2",
                        fontWeight: "700",
                    },
                ],

                "display-lg": [
                    "60px",
                    {
                        lineHeight: "1.1",
                        letterSpacing: "0",
                        fontWeight: "700",
                    },
                ],
            },

            boxShadow: {
                glow: "0 0 20px rgba(37,99,235,.25)",
                "glow-lg": "0 0 40px rgba(37,99,235,.25)",
                card: "0 20px 50px rgba(0,0,0,.45)",
            },

            backdropBlur: {
                xs: "2px",
            },

            keyframes: {
                rotate: {
                    from: {
                        transform: "rotate(0deg)",
                    },
                    to: {
                        transform: "rotate(360deg)",
                    },
                },

                pulse: {
                    "0%": {
                        opacity: ".4",
                        transform: "scale(1)",
                    },
                    "50%": {
                        opacity: "1",
                        transform: "scale(1.2)",
                    },
                    "100%": {
                        opacity: ".4",
                        transform: "scale(1)",
                    },
                },

                floatGlow: {
                    "0%": {
                        transform: "translate(0,0)",
                    },
                    "25%": {
                        transform: "translate(20px,-15px)",
                    },
                    "50%": {
                        transform: "translate(0,-30px)",
                    },
                    "75%": {
                        transform: "translate(-20px,-15px)",
                    },
                    "100%": {
                        transform: "translate(0,0)",
                    },
                },

                topologyFlow: {
                    from: {
                        strokeDashoffset: "100",
                    },
                    to: {
                        strokeDashoffset: "0",
                    },
                },
            },

            animation: {
                rotateSlow: "rotate 20s linear infinite",
                pulseGlow: "pulse 2s infinite",
                float: "floatGlow 8s ease-in-out infinite",
                topologyFlow: "topologyFlow 12s linear infinite",
            },
        },
    },

    plugins: [],
};
