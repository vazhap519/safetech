


"use client";

import Link from "next/link";
import { useState, useEffect } from "react";
import { usePathname } from "next/navigation";
import { formatPhone, formatPhoneInternational } from "@/lib/phone";

export default function Header({ settings }) {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const pathname = usePathname();

  const phone = formatPhone(settings?.contact_page?.phone);
  const intl = formatPhoneInternational(settings?.contact_page?.phone);

  // Scroll effect
  useEffect(() => {
    const handleScroll = () => {
      setScrolled(window.scrollY > 20);
    };
    window.addEventListener("scroll", handleScroll);
    return () => window.removeEventListener("scroll", handleScroll);
  }, []);

  const linkClass = (path) =>
    pathname === path
      ? "text-[#00C2A8] font-semibold"
      : "text-gray-700 hover:text-[#0B3C5D] transition";

  return (
    <>
      <header
        className={`fixed top-0 left-0 w-full z-50 transition-all duration-300 ${
          scrolled
            ? "bg-white/80 backdrop-blur-md shadow"
            : "bg-white"
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

          {/* LOGO */}
          <Link href="/" className="text-2xl font-bold">
            <span className="text-[#0B3C5D]">Safe</span>
            <span className="text-[#00C2A8]">tech</span>
          </Link>

          {/* DESKTOP NAV */}
          <nav className="hidden md:flex items-center gap-8">
            <Link href="/services" className={linkClass("/services")}>
              სერვისები
            </Link>

            <Link href="/about" className={linkClass("/about")}>
              ჩვენს შესახებ
            </Link>

            <Link href="/blog" className={linkClass("/blog")}>
              ბლოგი
            </Link>

            <Link href="/contact" className={linkClass("/contact")}>
              კონტაქტი
            </Link>
          </nav>

          {/* CTA (ALWAYS VISIBLE) */}
          <div className="flex items-center gap-3">

            {/* PHONE TEXT (desktop only) */}
            {phone && (
              <a
                href={`tel:+${intl}`}
                className="hidden lg:block text-sm text-gray-600 hover:text-[#0B3C5D]"
              >
                +995 {phone}
              </a>
            )}

            {/* BUTTON */}
            <a
              href={intl ? `tel:+${intl}` : "#"}
              className="flex items-center bg-[#00C2A8] text-white px-4 py-2 rounded-xl hover:bg-[#00a892] transition shadow-md active:scale-[0.97]"
            >
              📞
              <span className="hidden sm:inline ml-2">
                დაგვირეკე
              </span>
            </a>

            {/* MOBILE MENU BUTTON */}
            <button
              className="md:hidden text-3xl text-[#0B3C5D] ml-2"
              onClick={() => setOpen(!open)}
            >
              ☰
            </button>
          </div>
        </div>

        {/* MOBILE MENU */}
        {open && (
          <div className="md:hidden absolute top-full left-0 w-full bg-white px-4 pb-6 shadow-lg z-50 animate-in fade-in slide-in-from-top-2">
            <div className="flex flex-col gap-4 text-[#0B3C5D] mt-4">

              <Link href="/services" onClick={() => setOpen(false)}>
                სერვისები
              </Link>

              <Link href="/about" onClick={() => setOpen(false)}>
                ჩვენს შესახებ
              </Link>

              <Link href="/blog" onClick={() => setOpen(false)}>
                ბლოგი
              </Link>

              <Link href="/contact" onClick={() => setOpen(false)}>
                კონტაქტი
              </Link>

              {/* MOBILE CTA */}
              {intl && (
                <a
                  href={`tel:+${intl}`}
                  className="bg-[#00C2A8] text-white text-center py-3 rounded-xl mt-2"
                >
                  📞 {phone}
                </a>
              )}
            </div>
          </div>
        )}
      </header>

      {/* MOBILE FLOATING CALL BUTTON */}
      {intl && (
        <a
          href={`tel:+${intl}`}
          className="fixed bottom-6 right-6 bg-[#00C2A8] text-white p-4 rounded-full shadow-lg md:hidden z-50 active:scale-95"
        >
          📞
        </a>
      )}
    </>
  );
}