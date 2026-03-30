`use client`;

export default function EmptyState({
  title = "მონაცემები ვერ მოიძებნა",
  description = "ამ ეტაპზე ინფორმაცია არ არის ხელმისაწვდომი",
}) {
  return (
    <div className="min-h-[60vh] flex flex-col items-center justify-center text-center px-4">
      <div className="text-5xl mb-4">📭</div>

      <h2 className="text-xl md:text-2xl font-semibold text-[#0B3C5D]">
        {title}
      </h2>

      <p className="text-gray-500 mt-2 max-w-md">
        {description}
      </p>
    </div>
  );
}