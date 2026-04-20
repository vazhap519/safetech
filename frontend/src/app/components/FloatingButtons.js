"use client";

import { useState } from "react";
import { FaCommentDots, FaTimes, FaWhatsapp, FaViber } from "react-icons/fa";

function normalizePhone(value) {
  const digits = String(value || "").replace(/\D/g, "");

  if (!digits) return null;
  if (digits.startsWith("995")) return digits;

  return `995${digits.replace(/^0+/, "")}`;
}

export default function FloatingButtons({ settings }) {
  const [open, setOpen] = useState(false);

  const rawPhone =
    settings?.contact_page?.whatsapp ||
    settings?.contact_page?.phone ||
    settings?.contact?.find((item) => item.type === "phone")?.value;
  const phone = normalizePhone(rawPhone);

  if (!phone) return null;

  return (
    <div className="fixed bottom-24 md:bottom-10 right-5 z-50 flex flex-col items-end gap-3">
      <div
        className={`flex flex-col items-end gap-3 transition-all duration-300 ${
          open
            ? "opacity-100 translate-y-0"
            : "opacity-0 translate-y-5 pointer-events-none"
        }`}
      >
        <a
          href={`https://wa.me/${phone}`}
          target="_blank"
          rel="noopener noreferrer"
          aria-label="WhatsApp"
          className="bg-green-500 text-white w-12 h-12 flex items-center justify-center rounded-lg shadow-lg hover:scale-105 transition"
        >
          <FaWhatsapp size={20} />
        </a>

        <a
          href={`viber://chat?number=%2B${phone}`}
          aria-label="Viber"
          className="bg-purple-600 text-white w-12 h-12 flex items-center justify-center rounded-lg shadow-lg hover:scale-105 transition"
        >
          <FaViber size={20} />
        </a>
      </div>

      <button
        type="button"
        onClick={() => setOpen((value) => !value)}
        aria-label="საკონტაქტო ღილაკები"
        className="bg-[#00C2A8] text-white w-14 h-14 flex items-center justify-center rounded-lg shadow-xl hover:scale-105 transition"
      >
        {open ? <FaTimes size={18} /> : <FaCommentDots size={22} />}
      </button>
    </div>
  );
}
