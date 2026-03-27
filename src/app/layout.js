import "./globals.css";
import { Noto_Sans_Georgian } from "next/font/google";
import FloatingButtons from "./components/FloatingButtons";

import Header from "./components/Header";
import Footer from "./components/Footer";

// 🔥 დაამატე ეს
import { Toaster } from "react-hot-toast";

/* =========================
   FONT
========================= */
const geoFont = Noto_Sans_Georgian({
  subsets: ["georgian"],
  weight: ["400", "500", "600", "700"],
});

/* =========================
   GLOBAL SEO (IMPORTANT)
========================= */
export const metadata = {
  metadataBase: new URL("https://safetech.ge"),

  title: {
    default: "Safetech",
    template: "%s | Safetech",
  },

  description: "IT სერვისები საქართველოში",

  openGraph: {
    siteName: "Safetech",
    type: "website",
    locale: "ka_GE",
  },

  icons: {
    icon: "/favicon.ico",
  },
};

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