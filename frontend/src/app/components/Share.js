// export default function Share({ data, url: propUrl }) {
//   if (!data) return null;

//   const url =
//     propUrl ||
//     (typeof window !== "undefined" ? window.location.href : "");

//   return (
//     <div className="bg-white py-6 border-b">
//       <div className="max-w-7xl mx-auto px-4 flex flex-col items-center gap-4 text-center">

//         <p className="text-sm text-gray-600">
//           {data.title}
//         </p>

//         <div className="flex flex-wrap justify-center gap-3">

//           {data.buttons?.map((btn, i) => {
//             const Icon = icons[btn.icon];

//             if (btn.icon === "link") {
//               return <CopyButton key={i} url={url} />;
//             }

//             const shareUrl = btn.url.replace("{url}", url);

//             return (
//               <a
//                 key={i}
//                 href={shareUrl}
//                 target="_blank"
//                 rel="noopener noreferrer"
//                 className={`px-4 py-2 text-white rounded-lg text-sm flex items-center gap-2 hover:scale-105 transition ${btn.color}`}
//               >
//                 {Icon && <Icon size={16} />}
//                 {btn.name}
//               </a>
//             );
//           })}

//         </div>
//       </div>
//     </div>
//   );
// }   "use client";

import {
  FaFacebook,
  FaWhatsapp,
  FaTelegram,
  FaLinkedin,
  FaPinterest,
  FaTwitter,
  FaLink,
} from "react-icons/fa";
import toast from "react-hot-toast";

/* =========================
   ICON MAP
========================= */
const icons = {
  FaFacebook,
  FaWhatsapp,
  FaTelegram,
  FaLinkedin,
  FaPinterest,
  FaTwitter,
  FaLink,
};

/* =========================
   COPY BUTTON
========================= */
function CopyButton({ url }) {
  const handleCopy = async () => {
    await navigator.clipboard.writeText(url);
    toast.success("ლინკი დაკოპირდა ✅");
  };

  return (
    <button
      onClick={handleCopy}
      className="px-4 py-2 bg-gray-600 text-white rounded-lg text-sm hover:scale-105 transition flex items-center gap-2"
    >
      <FaLink size={16} />
      კოპირება
    </button>
  );
}

/* =========================
   SHARE COMPONENT
========================= */
export default function Share({ data, url: propUrl }) {
  if (!data || !data.length) return null;

  const url =
    propUrl ||
    (typeof window !== "undefined" ? window.location.href : "");

  const encodedUrl = encodeURIComponent(url);

  return (
    <div className="bg-white py-6 border-b">
      <div className="max-w-7xl mx-auto px-4 flex flex-col items-center gap-4 text-center">

        {data.title && (
          <p className="text-sm text-gray-600">
            {data.title}
          </p>
        )}

        <div className="flex flex-wrap justify-center gap-3">

          {data.map((btn, i) => {
            const Icon = icons[btn.icon];

            // 🔥 COPY BUTTON
            if (btn.icon === "FaLink") {
              return <CopyButton key={i} url={url} />;
            }

            const shareUrl = btn.url.replace("{url}", encodedUrl);

            return (
              <a
                key={i}
                href={shareUrl}
                target="_blank"
                rel="noopener noreferrer"
                className={`px-4 py-2 text-white rounded-lg text-sm flex items-center gap-2 hover:scale-105 transition ${btn.color}`}
              >
                {Icon && <Icon size={16} />}
                {btn.name}
              </a>
            );
          })}

        </div>
      </div>
    </div>
  );
}