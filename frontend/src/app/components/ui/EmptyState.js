"use client";

import {
  FaFacebookF,
  FaGithub,
  FaInstagram,
  FaLinkedinIn,
  FaTiktok,
  FaTwitter,
  FaYoutube,
} from "react-icons/fa";

const socialIcons = {
  facebook: FaFacebookF,
  instagram: FaInstagram,
  linkedin: FaLinkedinIn,
  twitter: FaTwitter,
  youtube: FaYoutube,
  tiktok: FaTiktok,
  github: FaGithub,
};

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

export default function EmptyState({
  empty,
  title,
  description,
  coming_soon,
}) {
  const finalTitle = title || empty?.title || "";
  const finalDescription = description || empty?.description || "";
  const finalComingSoon = coming_soon || empty?.coming_soon || "";
  const socials = empty?.socials || {};
  const hasSocials = Object.keys(socials).length > 0;

  if (!finalTitle && !finalDescription && !finalComingSoon && !hasSocials) {
    return null;
  }

  return (
    <div className="flex min-h-[70vh] items-center justify-center bg-gradient-to-br from-[#eef6fb] to-[#ffffff] px-4">
      <div className="w-full max-w-lg rounded-2xl border border-gray-100 bg-white/80 p-8 text-center shadow-xl backdrop-blur-md">
        {finalTitle ? (
          <h2 className="text-2xl font-bold text-[#0B3C5D] md:text-3xl">
            {finalTitle}
          </h2>
        ) : null}

        {finalDescription ? (
          <p className="mt-3 text-gray-500">{finalDescription}</p>
        ) : null}

        {finalComingSoon ? (
          <p className="mt-4 text-sm text-gray-400">{finalComingSoon}</p>
        ) : null}

        {hasSocials ? (
          <>
            <div className="my-6 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent" />
            <div className="flex flex-wrap justify-center gap-4">
              {Object.entries(socials).map(([platform, url]) => {
                const Icon = socialIcons[platform];
                const style = socialStyles[platform] || "bg-gray-200 text-black";

                if (!Icon || !url) return null;

                return (
                  <a
                    key={platform}
                    href={url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className={`flex h-10 w-10 items-center justify-center rounded-full ${style} transition hover:scale-110`}
                  >
                    <Icon />
                  </a>
                );
              })}
            </div>
          </>
        ) : null}
      </div>
    </div>
  );
}
