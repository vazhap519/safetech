"use client";

import { useEffect, useState } from "react";

export default function CopyButton({ url }) {
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    setMounted(true);
  }, []);

  if (!mounted) return null;

  const handleCopy = () => {
    navigator.clipboard.writeText(url);
    alert("ლინკი დაკოპირდა ✅");
  };

  return (
    <button
      onClick={handleCopy}
      className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm 
      hover:bg-gray-300 transition"
    >
      Copy Link
    </button>
  );
}