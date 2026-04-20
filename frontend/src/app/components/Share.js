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

const icons = {
  FaFacebook,
  FaWhatsapp,
  FaTelegram,
  FaLinkedin,
  FaPinterest,
  FaTwitter,
  FaLink,
};

const colorMap = {
  FaFacebook: "bg-blue-600 hover:bg-blue-700",
  FaWhatsapp: "bg-green-500 hover:bg-green-600",
  FaTelegram: "bg-sky-500 hover:bg-sky-600",
  FaLinkedin: "bg-blue-700 hover:bg-blue-800",
  FaPinterest: "bg-red-600 hover:bg-red-700",
  FaTwitter: "bg-black hover:bg-gray-800",
  FaLink: "bg-gray-600 hover:bg-gray-700",
};

function getButtons(data) {
  if (Array.isArray(data?.share_buttons)) return data.share_buttons;
  if (Array.isArray(data?.buttons)) return data.buttons;
  return [];
}

function fillTemplate(template, url, title) {
  const encodedUrl = encodeURIComponent(url);
  const encodedTitle = encodeURIComponent(title || "");

  return String(template || url)
    .replaceAll("{url}", encodedUrl)
    .replaceAll("{title}", encodedTitle)
    .replaceAll("{text}", encodedTitle);
}

function CopyButton({ url }) {
  const handleCopy = async () => {
    try {
      if (navigator.clipboard?.writeText) {
        await navigator.clipboard.writeText(url);
      } else {
        const input = document.createElement("input");
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand("copy");
        document.body.removeChild(input);
      }

      toast.success("ლინკი დაკოპირდა");
    } catch {
      toast.error("ლინკის დაკოპირება ვერ მოხერხდა");
    }
  };

  return (
    <button
      type="button"
      onClick={handleCopy}
      aria-label="ლინკის კოპირება"
      className="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm flex items-center gap-2 hover:bg-gray-700 transition"
    >
      <FaLink size={14} />
      კოპირება
    </button>
  );
}

export default function Share({ data, url: propUrl }) {
  const buttons = getButtons(data);

  if (buttons.length === 0) return null;

  const url =
    propUrl || data?.url || (typeof window !== "undefined" ? window.location.href : "");
  const title = data?.share_title || data?.title || "გააზიარე";

  if (!url) return null;

  return (
    <div className="py-10">
      <div className="max-w-4xl mx-auto px-4 text-center">
        {title && <p className="text-sm text-gray-500 mb-4">{title}</p>}

        <div className="flex flex-wrap justify-center gap-3">
          {buttons.map((btn, index) => {
            const Icon = icons[btn.icon] || FaLink;
            const isCopy = btn.type === "link" || btn.icon === "FaLink";

            if (isCopy) {
              return <CopyButton key={`${btn.name || "copy"}-${index}`} url={url} />;
            }

            return (
              <a
                key={`${btn.name || btn.type}-${index}`}
                href={fillTemplate(btn.url, url, title)}
                target="_blank"
                rel="noopener noreferrer"
                aria-label={btn.name || "Share"}
                className={`px-4 py-2 text-white rounded-lg text-sm flex items-center gap-2 transition ${colorMap[btn.icon] || "bg-gray-500 hover:bg-gray-600"}`}
              >
                <Icon size={14} />
                {btn.name}
              </a>
            );
          })}
        </div>
      </div>
    </div>
  );
}
