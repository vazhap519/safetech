

import "./globals.css";
import { Noto_Sans_Georgian } from "next/font/google";
import FloatingButtons from "./components/FloatingButtons";
import Header from "./components/Header";
import Footer from "./components/Footer";
import { getSettings } from "@/lib/datafetch";
import { Toaster } from "react-hot-toast";
const API_URL =
  process.env.NEXT_PUBLIC_API_URL || "http://127.0.0.1:8000";
/* =========================
   FONT
========================= */
const geoFont = Noto_Sans_Georgian({
  subsets: ["georgian"],
  weight: ["400", "500", "600", "700"],
});

/* =========================
   🔥 DYNAMIC SEO
========================= */
export async function generateMetadata() {
  const settings = await getSettings();

  const favicons = settings?.favicons || {};
  const version = settings?.favicon_version || 1;

  return {
    metadataBase: new URL(
      process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000"
    ),

    manifest: `${API_URL}/api/manifest.json`, // ✅ GLOBAL

    icons: {
      icon: [
        {
          url: `${favicons["32"]}?v=${version}`,
          sizes: "32x32",
          type: "image/png",
        },
        {
          url: `${favicons["16"]}?v=${version}`,
          sizes: "16x16",
          type: "image/png",
        },
      ],
      apple: [
        {
          url: `${favicons["apple"]}?v=${version}`,
          sizes: "180x180",
          type: "image/png",
        },
      ],
    },
  };
}
/* =========================
   LAYOUT
========================= */
export default function RootLayout({ children }) {
  return (
    <html lang="ka">
      <body className={`${geoFont.className} bg-darkbg text-white`}>
        
        <Header />

        {/* HEADER OFFSET */}
        <main className="pt-[80px] bg-[#F8FAFC] min-h-screen">
          {children}
        </main>

        <FloatingButtons />
        <Footer />

        {/* 🔥 TOAST GLOBAL */}
        <Toaster
          position="top-right"
          toastOptions={{
            duration: 3000,
            style: {
              borderRadius: "10px",
              background: "#0B3C5D",
              color: "#fff",
              fontSize: "14px",
            },
          }}
        />

      </body>
    </html>
  );
}