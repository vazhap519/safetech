"use client";

import { useState } from "react";
import { FaWhatsapp, FaViber } from "react-icons/fa";

export default function FloatingButtons({ settings }) {
  const [open, setOpen] = useState(false);

  const phone =
    settings?.contact?.find((item) => item.type === "phone")?.value;

  return (
    <div className="fixed bottom-24 md:bottom-10 right-5 z-50 flex flex-col items-end gap-3">

      {/* CHILD BUTTONS */}
      <div
        className={`flex flex-col items-end gap-3 transition-all duration-300 ${
          open
            ? "opacity-100 translate-y-0"
            : "opacity-0 translate-y-5 pointer-events-none"
        }`}
      >

        {/* WhatsApp */}
        <a
          href={`https://wa.me/995${phone}`}
          target="_blank"
          className="bg-green-500 text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:scale-110 transition"
        >
          <FaWhatsapp />
        </a>

        {/* Viber */}
        <a
          href={`viber://chat?number=995${phone}`}
          className="bg-purple-600 text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:scale-110 transition"
        >
          <FaViber />
        </a>

      </div>

      {/* MAIN BUTTON */}
      <button
        onClick={() => setOpen(!open)}
        className="bg-[#00C2A8] text-white w-14 h-14 flex items-center justify-center rounded-full shadow-xl hover:scale-110 transition"
      >
        {open ? "✕" : "💬"}
      </button>

    </div>
  );
}