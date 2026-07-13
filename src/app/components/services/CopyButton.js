"use client";

export default function CopyButton({ url }) {
  const handleCopy = async () => {
    await navigator.clipboard.writeText(url);
    window.alert("Link copied");
  };

  return (
    <button
      onClick={handleCopy}
      className="rounded-lg bg-gray-200 px-4 py-2 text-sm text-gray-700 transition hover:bg-gray-300"
      type="button"
    >
      Copy Link
    </button>
  );
}
