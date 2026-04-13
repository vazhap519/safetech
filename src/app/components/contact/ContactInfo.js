import { FaWhatsapp, FaViber } from "react-icons/fa";
import { formatPhone, formatPhoneInternational } from "@/lib/phone";

export default function ContactInfo({ data }) {
  const info = data?.info;

  const rawPhone = data?.phone;

  const phone = formatPhone(rawPhone);
  const intl = formatPhoneInternational(rawPhone);

  return (
    <div>
      <h2 className="text-2xl font-bold text-gray-900">
        {info?.title || ""}
      </h2>

      <div className="mt-6 flex flex-col gap-4">

        {/* PHONE */}
        {phone && (
          <a
            href={`tel:+${intl}`}
            className="
              flex items-center gap-3
              bg-white
              border border-gray-200
              p-5
              rounded-2xl
              transition-all duration-300
              hover:shadow-xl
              hover:-translate-y-1
            "
          >
            <span className="text-xl">📞</span>
            <span className="text-gray-900 font-semibold text-lg">
              {phone}
            </span>
          </a>
        )}

        {/* WHATSAPP */}
        {info?.whatsapp && (
          <a
            href={`https://wa.me/${intl}`}
            target="_blank"
            rel="noopener noreferrer"
            className="
              flex items-center
              bg-white
              border border-gray-200
              p-5
              rounded-2xl
              transition-all duration-300
              hover:shadow-xl
              hover:-translate-y-1
            "
          >
            <FaWhatsapp className="text-green-500 text-xl mr-3" />
            <span className="text-gray-900 font-semibold">
              WhatsApp
            </span>
          </a>
        )}

        {/* VIBER */}
        {info?.viber && (
          <a
            href={`viber://chat?number=%2B${intl}`}
            className="
              flex items-center
              bg-white
              border border-gray-200
              p-5
              rounded-2xl
              transition-all duration-300
              hover:shadow-xl
              hover:-translate-y-1
            "
          >
            <FaViber className="text-purple-500 text-xl mr-3" />
            <span className="text-gray-900 font-semibold">
              Viber
            </span>
          </a>
        )}

      </div>
    </div>
  );
}