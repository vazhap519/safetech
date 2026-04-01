"use client";

import { FaFacebookF, FaInstagram, FaGithub } from "react-icons/fa";

export default function EmptyState({
  title = "მონაცემები ვერ მოიძებნა",
  description = "ამ ეტაპზე ინფორმაცია არ არის ხელმისაწვდომი",
}) {
  return (
    <div className="min-h-[70vh] flex items-center justify-center px-4 bg-gradient-to-br from-[#eef6fb] to-[#ffffff]">
      
      <div className="max-w-lg w-full text-center bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 border border-gray-100 animate-fadeIn">
        
        {/* ICON */}
        <div className="text-6xl mb-4 animate-bounce">📭</div>

        {/* TITLE */}
        <h2 className="text-2xl md:text-3xl font-bold text-[#0B3C5D]">
          {title}
        </h2>

        {/* DESCRIPTION */}
        <p className="text-gray-500 mt-3">
          {description}
        </p>

        {/* COMING SOON TEXT */}
        <p className="mt-4 text-sm text-gray-400">
          🚀 გვერდზე მალე დაემატება ახალი კონტენტი
        </p>

        {/* DIVIDER */}
        <div className="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent" />

        {/* SOCIAL LINKS */}
        <div className="flex justify-center gap-4">
          <a
            href="https://facebook.com"
            target="_blank"
            className="w-10 h-10 flex items-center justify-center rounded-full bg-[#1877F2] text-white hover:scale-110 transition"
          >
            <FaFacebookF />
          </a>

          <a
            href="https://instagram.com"
            target="_blank"
            className="w-10 h-10 flex items-center justify-center rounded-full bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-white hover:scale-110 transition"
          >
            <FaInstagram />
          </a>

          <a
            href="https://github.com"
            target="_blank"
            className="w-10 h-10 flex items-center justify-center rounded-full bg-black text-white hover:scale-110 transition"
          >
            <FaGithub />
          </a>
        </div>

        {/* FOOTER TEXT */}
        <p className="text-xs text-gray-400 mt-6">
          © {new Date().getFullYear()} შენი პლატფორმა
        </p>
      </div>

      {/* ANIMATION STYLE */}
      <style jsx>{`
        .animate-fadeIn {
          animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }
      `}</style>
    </div>
  );
}