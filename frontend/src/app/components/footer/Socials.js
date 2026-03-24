"use client";

import * as FaIcons from "react-icons/fa";

export default function Socials({ socials }) {
  if (!socials?.length) return null;

  return (
    <div className="flex gap-3 mt-5">
      {socials
        .filter((item) => FaIcons[item.icon])
        .map((item) => {
          const Icon = FaIcons[item.icon];

          return (
            <a
              key={item.url}
              href={item.url}
              title={item.text}
              target="_blank"
              rel="noopener noreferrer"
              className="w-10 h-10 flex items-center justify-center rounded-full 
              transition-all duration-300 hover:-translate-y-1 hover:scale-110"
              style={{
                backgroundColor:
                  item.bg_color || "rgba(255,255,255,0.1)",
              }}
              onMouseEnter={(e) => {
                if (item.hover_color) {
                  e.currentTarget.style.backgroundColor =
                    item.hover_color;
                }
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.backgroundColor =
                  item.bg_color || "rgba(255,255,255,0.1)";
              }}
            >
              <Icon className="text-white text-lg" />
            </a>
          );
        })}
    </div>
  );
}