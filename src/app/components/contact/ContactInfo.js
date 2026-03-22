export default function ContactInfo() {
  return (
    <div>
      <h2 className="text-2xl font-bold text-[#0B3C5D]">
        საკონტაქტო ინფორმაცია
      </h2>

      <div className="mt-6 flex flex-col gap-4">

        <a
          href="tel:+995599000000"
          className="flex items-center gap-3 bg-white p-4 rounded-xl shadow hover:shadow-md transition"
        >
          <span className="text-xl">📞</span>
          <span className="text-gray-700 font-medium">
            +995 599 000 000
          </span>
        </a>

        <a
          href="https://wa.me/995599000000"
          target="_blank"
          className="flex items-center gap-3 bg-white p-4 rounded-xl shadow hover:shadow-md transition"
        >
          <span className="text-xl">🟢</span>
          <span className="text-gray-700 font-medium">
            WhatsApp ჩატი
          </span>
        </a>

        <a
          href="viber://chat?number=995599000000"
          className="flex items-center gap-3 bg-white p-4 rounded-xl shadow hover:shadow-md transition"
        >
          <span className="text-xl">🟣</span>
          <span className="text-gray-700 font-medium">
            Viber ჩატი
          </span>
        </a>

      </div>
    </div>
  );
}