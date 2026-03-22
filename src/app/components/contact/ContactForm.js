"use client";

import { useState } from "react";

export default function ContactForm() {
  const [form, setForm] = useState({
    name: "",
    phone: "",
    message: "",
  });

  const [loading, setLoading] = useState(false);
  const [sent, setSent] = useState(false);

  const handleChange = (e) => {
    setForm({
      ...form,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!form.name || !form.phone) {
      alert("შეავსე აუცილებელი ველები");
      return;
    }

    setLoading(true);

    try {
      // 🔥 აქ ჩასვამ Laravel API-ს შემდეგში
      await new Promise((res) => setTimeout(res, 1000));

      setSent(true);
      setForm({ name: "", phone: "", message: "" });

    } catch (err) {
      alert("დაფიქსირდა შეცდომა");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="bg-white p-6 rounded-2xl shadow-md border border-gray-100">

      <h2 className="text-xl font-bold text-[#0B3C5D]">
        დაგვიტოვე შეტყობინება
      </h2>

      {sent && (
        <div className="mt-4 bg-green-100 text-green-700 p-3 rounded-lg text-sm">
          შეტყობინება წარმატებით გაიგზავნა ✅
        </div>
      )}

      <form onSubmit={handleSubmit} className="mt-6 flex flex-col gap-4">

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
          value={form.phone}
          onChange={handleChange}
          placeholder="ტელეფონი"
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
          shadow-md hover:shadow-lg active:scale-95 disabled:opacity-70"
        >
          {loading ? "იგზავნება..." : "გაგზავნა"}
        </button>

      </form>
    </div>
  );
}