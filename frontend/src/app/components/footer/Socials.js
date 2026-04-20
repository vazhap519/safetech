"use client";

import * as FaIcons from "react-icons/fa";

const iconAliases = {
  facebook: "FaFacebook",
  instagram: "FaInstagram",
  linkedin: "FaLinkedin",
  twitter: "FaTwitter",
  x: "FaTwitter",
  youtube: "FaYoutube",
};

function normalizeIcon(icon) {
  if (!icon) return null;
  return iconAliases[String(icon).toLowerCase()] || icon;
}

function normalizeUrl(url) {
  const value = String(url || "").trim();

  if (!value || value === "#") return null;
  if (/^(https?:|mailto:|tel:|viber:)/i.test(value)) return value;
  if (value.startsWith("//")) return `https:${value}`;

  return `https://${value.replace(/^\/+/, "")}`;
}

export default function Socials({ socials }) {
  const items = Array.isArray(socials)
    ? socials
        .map((item) => ({
          ...item,
          icon: normalizeIcon(item?.icon),
          url: normalizeUrl(item?.url),
        }))
        .filter((item) => item.icon && item.url && FaIcons[item.icon])
    : [];

  if (items.length === 0) return null;

  return (
    <div className="flex gap-3 mt-5">
      {items.map((item, index) => {
        const Icon = FaIcons[item.icon];
        const bg = item.bg_color || "rgba(255,255,255,0.1)";
        const hover = item.hover_color || "#00C2A8";

        return (
          <a
            key={`${item.url}-${index}`}
            href={item.url}
            title={item.text || item.icon}
            aria-label={item.text || item.icon}
            target="_blank"
            rel="noopener noreferrer"
            className="w-10 h-10 flex items-center justify-center rounded-lg transition-all duration-300 hover:-translate-y-1 hover:scale-105"
            style={{ backgroundColor: bg }}
            onMouseEnter={(e) => {
              e.currentTarget.style.backgroundColor = hover;
            }}
            onMouseLeave={(e) => {
              e.currentTarget.style.backgroundColor = bg;
            }}
          >
            <Icon className="text-white text-lg" />
          </a>
        );
      })}
    </div>
  );
}
