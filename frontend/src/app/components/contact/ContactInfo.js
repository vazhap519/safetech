import { FaWhatsapp, FaViber } from "react-icons/fa";
import { formatPhone, formatPhoneInternational } from "@/lib/phone";

export default function ContactInfo({ data }) {
  const rawPhone = data?.phone;

  const phone = formatPhone(rawPhone);
  const intl = formatPhoneInternational(rawPhone);

  return (
    <div>
      <h2 className="text-2xl font-bold text-[#0B3C5D]">
        {data?.contact_info_title}
      </h2>

      <div className="mt-6 flex flex-col gap-4">

        {phone && (
          <a href={`tel:+${intl}`} className="flex items-center gap-3 bg-white p-4 rounded-xl shadow">
            📞<span className="text-[#0B3C5D] font-medium">{phone}</span>
          </a>
        )}

     {/* WHATSAPP */}
{intl && (
  <a
    href={`https://wa.me/${intl}`}
    target="_blank"
    className="flex items-center bg-white p-4 rounded-xl shadow hover:shadow-md transition hover:bg-gray-50 active:scale-[0.98]"
  >
    <FaWhatsapp className="text-green-500 text-xl mr-3" />
    
    <span className="text-[#0B3C5D] font-semibold">
      {phone}
    </span>
  </a>
)}


{intl && (
  <a
    href={`viber://chat?number=%2B${intl}`}
    className="flex items-center bg-white p-4 rounded-xl shadow hover:shadow-md transition hover:bg-gray-50 active:scale-[0.98]"
  >
    <FaViber className="text-purple-500 text-xl mr-3" />
    
    <span className="text-[#0B3C5D] font-semibold">
      {phone}
    </span>
  </a>
)}

      </div>
    </div>
  );
}