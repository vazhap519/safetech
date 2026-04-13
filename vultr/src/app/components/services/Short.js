// export default function Short({ short }) {
//   if (!short) return null;

//   return (
//     <section className="py-12 bg-white">
//       <div className="max-w-4xl mx-auto px-4">

//         <div className="bg-[#F8FAFC] p-6 md:p-8 rounded-2xl shadow-sm text-center">
//           <p className="text-gray-700 text-lg leading-relaxed">
//             {short}
//           </p>
//         </div>

//       </div>
//     </section>
//   );
// }

export default function Short({ short }) {
  if (!short) return null;

  return (
    <section className="py-20 bg-white">
      <div className="max-w-4xl mx-auto px-6">

        <div className="
          bg-gray-50
          border border-gray-200
          rounded-3xl
          p-8 md:p-12
          shadow-md
          text-center
        ">

          <p className="
            text-lg md:text-xl
            text-gray-800
            leading-relaxed
            max-w-2xl
            mx-auto
          ">
            {short}
          </p>

        </div>

      </div>
    </section>
  );
}