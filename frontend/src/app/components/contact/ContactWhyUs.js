// export default function ContactWhyUs({ data }) {
//   return (
//     <div className="mt-10">
//       <h3 className="text-xl font-bold text-[#0B3C5D]">
//         {data?.why_title}
//       </h3>

//       <div className="mt-6 flex flex-col gap-4">
//         {data?.why_items?.map((item, i) => (
//           <div
//             key={i}
//             className="flex items-start gap-3 bg-white p-4 rounded-xl shadow hover:shadow-md transition hover:bg-gray-50"
//           >
//             {/* ICON */}
//             <div className="mt-1 w-6 h-6 flex items-center justify-center rounded-full bg-[#00C2A8]/10 text-[#00C2A8] text-sm font-bold">
//               ✓
//             </div>

//             {/* TEXT */}
//             <p className="text-[#0B3C5D] font-medium leading-relaxed">
//               {item?.text}
//             </p>
//           </div>
//         ))}
//       </div>
//     </div>
//   );
// }

export default function ContactWhyUs({ data }) {
  return (
    <div className="mt-12">

      <h3 className="text-xl font-semibold text-gray-900">
        {data?.why_title}
      </h3>

      <div className="mt-6 flex flex-col gap-4">

        {data?.why_items?.map((item, i) => (
          <div
            key={i}
            className="
              flex items-start gap-3
              bg-white
              border border-gray-200
              p-4
              rounded-2xl
              transition-all duration-300
              hover:shadow-lg
              hover:-translate-y-1
            "
          >
            {/* ICON */}
            <div className="
              w-8 h-8 flex items-center justify-center
              rounded-lg
              bg-[#00C2A8]/10
              text-[#00C2A8]
              font-bold
            ">
              ✓
            </div>

            {/* TEXT */}
            <p className="text-gray-700 leading-relaxed">
              {item?.text}
            </p>

          </div>
        ))}

      </div>
    </div>
  );
}