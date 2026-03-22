import Link from "next/link";

export default function Footer() {
  return (
    <footer className="bg-[#0B3C5D] text-white">
      <div className="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-8 text-center md:text-left">

        {/* Brand */}
        <div className="flex flex-col items-center md:items-start">
          <h2 className="text-2xl font-bold mb-4">
            <span>Safe</span>
            <span className="text-[#00C2A8]">tech</span>
          </h2>

          <p className="text-sm text-gray-300 max-w-xs">
            უსაფრთხოების კამერები, ქსელური სერვისები და IT მხარდაჭერა საქართველოში.
          </p>

          {/* 🔥 SOCIAL */}
          <div className="flex gap-3 mt-5">

            {/* Facebook */}
            <a
              href="#"
              className="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 
              hover:bg-[#1877F2] hover:-translate-y-1 transition-all duration-300"
            >
              <span>📘</span>
            </a>

            {/* WhatsApp */}
            <a
              href="https://wa.me/995599000000"
              className="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 
              hover:bg-green-500 hover:-translate-y-1 transition-all duration-300"
            >
              <span>💬</span>
            </a>

            {/* Email */}
            <a
              href="mailto:info@safetech.ge"
              className="w-10 h-10 flex items-center justify-center rounded-full bg-white/10 
              hover:bg-[#00C2A8] hover:-translate-y-1 transition-all duration-300"
            >
              <span>✉️</span>
            </a>

          </div>
        </div>

        {/* Services */}
        <div className="flex flex-col items-center md:items-start">
          <h3 className="font-semibold mb-4">სერვისები</h3>
          <ul className="space-y-2 text-sm text-gray-300">
            <li><Link href="/services/cctv" className="hover:text-[#00C2A8] transition">CCTV მონტაჟი</Link></li>
            <li><Link href="/services/pos" className="hover:text-[#00C2A8] transition">POS სისტემები</Link></li>
            <li><Link href="/services/internet" className="hover:text-[#00C2A8] transition">ინტერნეტი</Link></li>
            <li><Link href="/services/router" className="hover:text-[#00C2A8] transition">Router Setup</Link></li>
            <li><Link href="/services/os" className="hover:text-[#00C2A8] transition">OS ინსტალაცია</Link></li>
          </ul>
        </div>

        {/* Company */}
        <div className="flex flex-col items-center md:items-start">
          <h3 className="font-semibold mb-4">კომპანია</h3>
          <ul className="space-y-2 text-sm text-gray-300">
            <li><Link href="/about" className="hover:text-[#00C2A8] transition">ჩვენს შესახებ</Link></li>
            <li><Link href="/blog" className="hover:text-[#00C2A8] transition">ბლოგი</Link></li>
            <li><Link href="/contact" className="hover:text-[#00C2A8] transition">კონტაქტი</Link></li>
            <li><Link href="/privacy" className="hover:text-[#00C2A8] transition">Privacy Policy</Link></li>
          </ul>
        </div>

        {/* Contact */}
        <div className="flex flex-col items-center md:items-start">
          <h3 className="font-semibold mb-4">კონტაქტი</h3>
          <ul className="space-y-2 text-sm text-gray-300">
            <li>📞 +995 599 000 000</li>
            <li>📧 info@safetech.ge</li>
            <li>📍 Service Area: Georgia</li>
          </ul>
        </div>

      </div>

      {/* Bottom */}
      <div className="border-t border-gray-600 text-center py-4 text-sm text-gray-400">
        © 2026 Safetech. ყველა უფლება დაცულია.
      </div>
    </footer>
  );
}