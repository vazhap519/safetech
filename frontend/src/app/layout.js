

import "./globals.css";
import FloatingButtons from "./components/FloatingButtons";
import Header from "./components/Header";
import Footer from "./components/Footer";
import { getSettings } from "@/lib/datafetch";
import { Toaster } from "react-hot-toast";

/* =========================
   🔥 DYNAMIC SEO
========================= */
export async function generateMetadata() {
  const settings = await getSettings();

  const favicons = settings?.favicons || {};
  const version = settings?.favicon_version || 1;
  const icon = (url, sizes) =>
    url
      ? {
          url: `${url}?v=${version}`,
          sizes,
          type: "image/png",
        }
      : null;
  const iconList = [icon(favicons["32"], "32x32"), icon(favicons["16"], "16x16")].filter(Boolean);
  const appleIcon = icon(favicons["apple"], "180x180");

  return {
    metadataBase: new URL(
      process.env.NEXT_PUBLIC_SITE_URL || "http://localhost:3000"
    ),

    manifest: "/manifest.json",

    icons: {
      ...(iconList.length > 0 && { icon: iconList }),
      ...(appleIcon && { apple: [appleIcon] }),
    },
  };
}
/* =========================
   LAYOUT
========================= */
export default async function RootLayout({ children }) {
  const settings = await getSettings().catch(() => null);

  return (
    <html lang="ka">
      <body className="bg-darkbg text-white">
        
        <Header settings={settings} />

        {/* HEADER OFFSET */}
        <main className="pt-[80px] bg-[#F8FAFC] min-h-screen">
          {children}
        </main>

        <FloatingButtons settings={settings} />
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
