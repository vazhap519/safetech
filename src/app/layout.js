import "./globals.css";
import { Noto_Sans_Georgian } from "next/font/google";
import FloatingButtons from "./components/FloatingButtons";

import Header from "./components/Header";
import Footer from "./components/Footer";

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
    default: "Safetech | IT სერვისები და კამერების მონტაჟი თბილისში",
    template: "%s | Safetech",
  },

  description:
    "Safetech გთავაზობთ კამერების მონტაჟს, POS სისტემებს, ინტერნეტის გაყვანას და IT სერვისებს თბილისში და მთელ საქართველოში.",

  openGraph: {
    siteName: "Safetech",
    type: "website",
    locale: "ka_GE",
    url: "https://safetech.ge",
    images: [
      {
        url: "/services/1.jpg",
        width: 1200,
        height: 630,
      },
    ],
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

      </body>
    </html>
  );
}