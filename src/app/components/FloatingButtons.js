"use client";

import { useState } from "react";

export default function FloatingButtons() {
  const [open, setOpen] = useState(false);

  return (
<div className="fixed bottom-24 md:bottom-10 right-5 z-50 flex flex-col items-end gap-3">
      {/* CHILD BUTTONS */}
      <div
        className={`flex flex-col items-end gap-3 transition-all duration-300 ${
          open ? "opacity-100 translate-y-0" : "opacity-0 translate-y-5 pointer-events-none"
        }`}
      >
        {/* WhatsApp */}
        <a
          href="https://wa.me/995599000000"
          target="_blank"
          className="bg-green-500 text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:scale-110 transition"
        >
          💬
        </a>

        {/* Viber */}
        <a
          href="viber://chat?number=995599000000"
          className="bg-purple-600 text-white w-12 h-12 flex items-center justify-center rounded-full shadow-lg hover:scale-110 transition"
        >
          📞
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