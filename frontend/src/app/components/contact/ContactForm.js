"use client";

import { useState } from "react";
import { sendContact } from "@/lib/datafetch";
import toast from "react-hot-toast";
import { z } from "zod";

/* =========================
   VALIDATION SCHEMA
========================= */
const schema = z.object({
  name: z.string().min(2, "სახელი მინიმუმ 2 სიმბოლო"),
  phone: z
    .string()
    .min(9, "ტელეფონი არასრულია")
    .regex(/^\d+$/, "ტელეფონი უნდა შეიცავდეს მხოლოდ ციფრებს"),
  message: z.string().optional(),
});

/* =========================
   PHONE FORMAT (UI only)
========================= */
const formatPhone = (value) => {
  const cleaned = value.replace(/\D/g, "");

  return cleaned
    .replace(/(\d{3})(\d{2})(\d{2})(\d{0,2})/, (_, a, b, c, d) =>
      [a, b, c, d].filter(Boolean).join(" ")
    )
    .trim();
};

export default function ContactForm() {
  const [form, setForm] = useState({
    name: "",
    phone: "",
    message: "",
    website: "", // 🔒 honeypot
  });

  const [loading, setLoading] = useState(false);

  /* =========================
     CHANGE HANDLER
  ========================= */
  const handleChange = (e) => {
    const { name, value } = e.target;

    if (name === "phone") {
      const cleaned = value.replace(/\D/g, "");
      setForm((prev) => ({ ...prev, phone: cleaned }));
      return;
    }

    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  /* =========================
     SUBMIT
  ========================= */
  const handleSubmit = async (e) => {
    e.preventDefault();

    // 🔒 honeypot
    if (form.website) return;

    const cleaned = {
      ...form,
      phone: form.phone.replace(/\D/g, ""),
    };

    const result = schema.safeParse(cleaned);

    if (!result.success) {
      const firstError =
        result.error.issues?.[0]?.message || "დაფიქსირდა შეცდომა";

      toast.error(firstError);
      return;
    }

    setLoading(true);
    const toastId = toast.loading("იგზავნება...");

    try {
      const res = await sendContact(cleaned);

      if (res?.error) throw new Error();

      toast.success("შეტყობინება გაიგზავნა ✅", { id: toastId });

      setForm({
        name: "",
        phone: "",
        message: "",
        website: "",
      });

    } catch {
      toast.error("დაფიქსირდა შეცდომა ❌", { id: toastId });
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-white p-6 rounded-2xl shadow-md border border-gray-100">

      <h2 className="text-xl font-bold text-[#0B3C5D]">
        დაგვიტოვე შეტყობინება
      </h2>

      <form onSubmit={handleSubmit} className="mt-6 flex flex-col gap-4">

        {/* 🔒 HONEYPOT */}
        <input
          type="text"
          name="website"
          value={form.website}
          onChange={handleChange}
          autoComplete="off"
          tabIndex="-1"
          className="hidden"
        />

        {/* NAME */}
        <input
          type="text"
          name="name"
          value={form.name}
          onChange={handleChange}
          placeholder="სახელი"
          className="border border-gray-300 p-3 rounded-lg 
          text-black placeholder-gray-400
          focus:ring-2 focus:ring-[#00C2A8] outline-none transition"
        />

        {/* PHONE */}
        <input
          type="tel"
          name="phone"
          value={formatPhone(form.phone)}
          onChange={handleChange}
          placeholder="555 12 34 56"
          className="border border-gray-300 p-3 rounded-lg 
          text-black placeholder-gray-400
          focus:ring-2 focus:ring-[#00C2A8] outline-none transition"
        />

        {/* MESSAGE */}
        <textarea
          name="message"
          value={form.message}
          onChange={handleChange}
          placeholder="მოგვწერე დეტალები..."
          rows="5"
          className="border border-gray-300 p-3 rounded-lg 
          text-black placeholder-gray-400
          focus:ring-2 focus:ring-[#00C2A8] outline-none transition"
        />

        {/* BUTTON */}
        <button
          type="submit"
          disabled={loading}
          className="bg-[#00C2A8] text-white py-3 rounded-xl 
          hover:bg-[#00a892] transition 
          shadow-md hover:shadow-lg active:scale-95 
          disabled:opacity-70 disabled:cursor-not-allowed"
        >
          {loading ? "იგზავნება..." : "გაგზავნა"}
        </button>

      </form>
    </div>
  );
}