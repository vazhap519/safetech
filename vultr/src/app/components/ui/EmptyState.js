"use client";

import { useEffect, useState } from "react";
import {
  FaFacebookF,
  FaInstagram,
  FaLinkedinIn,
  FaTwitter,
  FaYoutube,
  FaTiktok,
  FaGithub,
} from "react-icons/fa";

/* 🔥 ICON MAP */
const socialIcons = {
  facebook: FaFacebookF,
  instagram: FaInstagram,
  linkedin: FaLinkedinIn,
  twitter: FaTwitter,
  youtube: FaYoutube,
  tiktok: FaTiktok,
  github: FaGithub,
};

/* 🎨 COLORS */
const socialStyles = {
  facebook: "bg-[#1877F2] text-white",
  instagram:
    "bg-gradient-to-tr from-yellow-400 via-pink-500 to-purple-600 text-white",
  linkedin: "bg-[#0A66C2] text-white",
  twitter: "bg-[#1DA1F2] text-white",
  youtube: "bg-[#FF0000] text-white",
  tiktok: "bg-black text-white",
  github: "bg-black text-white",
};

/* 🔥 LOCAL FALLBACK SOCIALS */
const defaultSocials = {
  facebook: "#",
  instagram: "#",
  linkedin: "#",
  twitter: "#",
  youtube: "#",
  tiktok: "#",
  github: "#",
};

export default function EmptyState({
  empty,
  title,
  description,
  coming_soon,
}) {
const year = new Date().getFullYear();

  /* 🔥 TEXT FALLBACK */
  const finalTitle =
    title || empty?.title || "მონაცემები ვერ მოიძებნა";

  const finalDescription =
    description ||
    empty?.description ||
    "ამ ეტაპზე ინფორმაცია არ არის ხელმისაწვდომი";

  const finalComingSoon =
    coming_soon ||
    empty?.coming_soon ||
    "🚀 გვერდზე მალე დაემატება ახალი კონტენტი";

  /* 🔗 SOCIALS */
  const socials = empty?.socials || defaultSocials;

  return (
    <div className="min-h-[70vh] flex items-center justify-center px-4 bg-gradient-to-br from-[#eef6fb] to-[#ffffff]">
      <div className="max-w-lg w-full text-center bg-white/80 backdrop-blur-md shadow-xl rounded-2xl p-8 border border-gray-100">

        <div className="text-6xl mb-4 animate-bounce">📭</div>

        <h2 className="text-2xl md:text-3xl font-bold text-[#0B3C5D]">
          {finalTitle}
        </h2>

        <p className="text-gray-500 mt-3">
          {finalDescription}
        </p>

        <p className="mt-4 text-sm text-gray-400">
          {finalComingSoon}
        </p>

        <div className="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent" />

        {/* 🔥 SOCIALS */}
        <div className="flex justify-center gap-4 flex-wrap">
          {Object.entries(socials).map(([platform, url]) => {
            const Icon = socialIcons[platform];
            const style =
              socialStyles[platform] || "bg-gray-200 text-black";

            if (!Icon) return null;

            return (
              <a
                key={platform}
                href={url || "#"}
                target="_blank"
                rel="noopener noreferrer"
                className={`w-10 h-10 flex items-center justify-center rounded-full ${style} hover:scale-110 transition`}
              >
                <Icon />
              </a>
            );
          })}
        </div>

        <p className="text-xs text-gray-400 mt-6">
          © {year}
        </p>
      </div>
    </div>
  );
}