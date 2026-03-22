"use client";

import Link from "next/link";
import { useState, useEffect } from "react";
import { usePathname } from "next/navigation";

export default function Header() {
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);
  const pathname = usePathname();

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
      : "text-gray-700 hover:text-black";

  return (
    <header
      className={`fixed top-0 left-0 w-full z-50 transition ${
        scrolled
          ? "bg-white/80 backdrop-blur-md shadow"
          : "bg-white"
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">

        {/* Logo */}
        <Link href="/" className="text-2xl font-bold">
          <span className="text-[#0B3C5D]">Safe</span>
          <span className="text-[#00C2A8]">tech</span>
        </Link>

        {/* Desktop Menu */}
        <nav className="hidden md:flex items-center gap-8">

          {/* Services Dropdown */}
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

        {/* CTA */}
        <div className="hidden md:flex items-center gap-4">
          <a href="tel:+995599000000" className="text-sm text-gray-600">
            +995 599 000 000
          </a>

          <a
            href="tel:+995599000000"
            className="bg-[#00C2A8] text-white px-4 py-2 rounded-xl hover:bg-[#00a892] transition shadow-md"
          >
            დაგვირეკე
          </a>
        </div>

        {/* Mobile Button */}
        <button
          className="md:hidden text-3xl text-[#0B3C5D]"
  onClick={() => setOpen(!open)}
       
        >
          ☰
        </button>
      </div>

      {/* Mobile Menu */}
      {open && (
  <div className="md:hidden absolute top-full left-0 w-full bg-white px-4 pb-6 shadow-lg z-50">      
      <div className="flex flex-col gap-4  text-[#0B3C5D]">

            <Link    className="text-[#0B3C5D] font-medium" href="/services" onClick={() => setOpen(false)}>
              სერვისები
            </Link>

            <Link  className="text-[#0B3C5D] font-medium" href="/about" onClick={() => setOpen(false)}>
              ჩვენს შესახებ
            </Link>

            <Link  className="text-[#0B3C5D] font-medium" href="/blog" onClick={() => setOpen(false)}>
              ბლოგი
            </Link>

            <Link  className="text-[#0B3C5D] font-medium" href="/contact" onClick={() => setOpen(false)}>
              კონტაქტი
            </Link>

            <a
              href="tel:+995599000000"
              className="bg-[#00C2A8] text-white text-center py-3 rounded-xl mt-2"
            >
              📞 დაგვირეკე
            </a>
          </div>
        </div>
      )}
    </header>
  );
}
