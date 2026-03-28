/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./app/**/*.{js,ts,jsx,tsx}",       // ✅ next 13+
    "./components/**/*.{js,ts,jsx,tsx}",
    "./src/**/*.{js,ts,jsx,tsx}",       // ✅ შენიც დავტოვეთ
  ],
  theme: {
    extend: {

    colors: {
  // 🔵 PRIMARY BRAND
  primary: {
    DEFAULT: "#00C2FF",
    50: "#E6F9FF",
    100: "#B3ECFF",
    200: "#80DFFF",
    300: "#4DD2FF",
    400: "#1AC5FF",
    500: "#00C2FF",
    600: "#0099CC",
    700: "#007399",
    800: "#004C66",
    900: "#002633",
  },

  // 🌑 DARK BASE (IMPORTANT FIX)
  dark: {
    bg: "#071A2B",
    surface: "#0F2538",
    elevated: "#132E46",
    border: "rgba(255,255,255,0.08)",
  },

  // 🌫 GLASS SYSTEM (NEW)
  glass: {
    DEFAULT: "rgba(255,255,255,0.05)",
    light: "rgba(255,255,255,0.08)",
    strong: "rgba(255,255,255,0.12)",
  },

  // ⚡ ACCENT (CTA + ACTIONS)
  accent: {
    DEFAULT: "#00E0B8",
    hover: "#00C2A0",
  },

  // ⚠️ STATUS COLORS
  success: "#22C55E",
  warning: "#F59E0B",
  danger: "#EF4444",

  // ⚪ TEXT SYSTEM (CRITICAL)
  text: {
    primary: "#E6F0F8",
    secondary: "#9FB3C8",
    muted: "#6B859E",
  },

  // 🧱 BACKGROUND LAYERS
  bg: {
    base: "#071A2B",
    section: "#0A1F33",
    soft: "#0F2538",
  }
}

    },
  },
  plugins: [],
};