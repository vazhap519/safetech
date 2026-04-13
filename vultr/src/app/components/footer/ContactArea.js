// export default function ContactArea({ contact }) {
//   console.log(contact)
//   if (!contact?.length) return null;

//   return (
//     <ul className="space-y-2 text-sm text-gray-300">
//       {contact.map((item, index) => (
//         <li key={index}>
//           {item.label || item.value}
//         </li>
//       ))}
//     </ul>
//   );
// }


"use client";

import * as FaIcons from "react-icons/fa";

export default function ContactArea({ contact }) {
  if (!contact?.length) return null;

  return (
    <ul className="space-y-2 text-sm text-gray-300">
      {contact.map((item, index) => {
        const Icon = FaIcons[item.icon];

        // ✅ ეს გაკლდა
        const href =
          item.prefix && item.value
            ? item.prefix + item.value
            : null;

        return (
          <li key={index} className="flex items-center gap-3">

            {/* ICON */}
            {Icon && (
              <div className="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-[#00C2A8] transition">
                <Icon className="text-white text-sm" />
              </div>
            )}

            {/* TEXT */}
            {href ? (
              <a href={href} className="hover:text-white transition">
                {item.label || item.value}
              </a>
            ) : (
              <span>{item.label || item.value}</span>
            )}

          </li>
        );
      })}
    </ul>
  );
}