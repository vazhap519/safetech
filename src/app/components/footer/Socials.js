"use client";

import * as FaIcons from "react-icons/fa";

export default function Socials({ socials }) {
  if (!Array.isArray(socials) || socials.length === 0) return null;

  return (
    <div className="flex gap-3 mt-5">
      {socials
        .filter((item) => item?.icon && FaIcons[item.icon])
        .map((item, index) => {
          const Icon = FaIcons[item.icon];

          const bg = item.bg_color || "rgba(255,255,255,0.1)";
          const hover = item.hover_color || "#00C2A8";

          return (
            <a
              key={`${item.url}-${index}`}
              href={item.url || "#"}
              title={item.text || ""}
              target="_blank"
              rel="noopener noreferrer"
              className="w-10 h-10 flex items-center justify-center rounded-full transition-all duration-300 hover:-translate-y-1 hover:scale-110"
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