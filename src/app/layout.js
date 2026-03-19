import "./globals.css";
import { Inter } from "next/font/google";
import TopBar from "./components/TopBar";
import Header from "./components/Header";
import { getSettings } from "@/lib/settings";

const inter = Inter({
  subsets: ["latin"],
});

export const metadata = {
  title: "SafeTech | IT Security Systems",
  description: "IT Security Systems",
};

export default async function RootLayout({ children }) {
  const settings = await getSettings();
  return (
    <html lang="en">
      <body className={`${inter.className} bg-darkbg text-white`}>
        
        <TopBar settings={settings} />
<Header settings={settings}/>
        {children}

      </body>
    </html>
  );
}