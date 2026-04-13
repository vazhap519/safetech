"use client";

import {
  FaFacebook,
  FaWhatsapp,
  FaTelegram,
  FaLinkedin,
  FaPinterest,
  FaTwitter,
  FaLink,
} from "react-icons/fa";
import toast from "react-hot-toast";

/* =========================
   ICON MAP
========================= */
const icons = {
  FaFacebook,
  FaWhatsapp,
  FaTelegram,
  FaLinkedin,
  FaPinterest,
  FaTwitter,
  FaLink,
};

/* =========================
   COLOR FALLBACK (🔥 FIX)
========================= */
const colorMap = {
  FaFacebook: "bg-blue-600 hover:bg-blue-700",
  FaWhatsapp: "bg-green-500 hover:bg-green-600",
  FaTelegram: "bg-sky-500 hover:bg-sky-600",
  FaLinkedin: "bg-blue-700 hover:bg-blue-800",
  FaPinterest: "bg-red-600 hover:bg-red-700",
  FaTwitter: "bg-black hover:bg-gray-800",
  FaLink: "bg-gray-600 hover:bg-gray-700",
};

/* =========================
   COPY BUTTON
========================= */
function CopyButton({ url }) {
  const handleCopy = async () => {
    await navigator.clipboard.writeText(url);
    toast.success("ლინკი დაკოპირდა ✅");
  };

  return (
    <button
      onClick={handleCopy}
      className="px-4 py-2 bg-gray-600 text-white rounded-full text-sm flex items-center gap-2 hover:scale-105 transition"
    >
      <FaLink size={14} />
      კოპირება
    </button>
  );
}

/* =========================
   SHARE COMPONENT
========================= */
export default function Share({ data, url: propUrl }) {
  if (!data || !Array.isArray(data.share_buttons)) return null;

  const url =
    propUrl ||
    (typeof window !== "undefined" ? window.location.href : "");

  const encodedUrl = encodeURIComponent(url);

  return (
    <div className="py-10">
      <div className="max-w-4xl mx-auto px-4 text-center">

        {/* TITLE */}
        {data.share_title && (
          <p className="text-sm text-gray-500 mb-4">
            {data.share_title}
          </p>
        )}

        {/* BUTTONS */}
        <div className="flex flex-wrap justify-center gap-3">

          {data.share_buttons.map((btn, i) => {
            const Icon = icons[btn.icon];

            if (btn.icon === "FaLink") {
              return <CopyButton key={i} url={url} />;
            }

            const shareUrl = btn.url.replace("{url}", encodedUrl);

            return (
              <a
                key={i}
                href={shareUrl}
                target="_blank"
                rel="noopener noreferrer"
                className={`
                  px-4 py-2
                  text-white
                  rounded-full
                  text-sm
                  flex items-center gap-2
                  transition
                  hover:scale-105
                  ${colorMap[btn.icon] || "bg-gray-500"}
                `}
              >
                {Icon && <Icon size={14} />}
                {btn.name}
              </a>
            );
          })}

        </div>
      </div>
    </div>
  );
}