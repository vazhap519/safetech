/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./src/**/*.{js,ts,jsx,tsx}"
  ],
  theme: {
    extend: {
      colors: {
        primary: "#00C2FF",
        darkbg: "#071A2B",
        card: "#0F2538",
        footer: "#050F1A"
      }
    }
  },
  plugins: []
};