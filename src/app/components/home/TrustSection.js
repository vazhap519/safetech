// import Image from "next/image";
// export default function TrustSection({data}) {
//   console.log('ნდობა',data)
//   const logos=data;
//   return (
//     <section className="py-12 bg-gray-50">
//       <div className="max-w-6xl mx-auto px-4 text-center">
//         <p className="text-gray-500 mb-6">
//           გვენდობიან
//         </p>

//         <div className="flex flex-wrap justify-center gap-8 opacity-70">
//         {logos?.map((logo, i) => (
//   <Image
//     key={i}
//     src={logo?.image}
//     alt="client"
//     className="h-10 object-contain opacity-70 hover:opacity-100 transition"
//   />
// ))}
//         </div>
//       </div>
//     </section>
//   );
// }



"use client";
import Image from "next/image";

export default function TrustSection({ data = [] }) {
  return (
    <section className="py-20 bg-gradient-to-b from-[#071A2B] via-[#0B3C5D] to-[#071A2B] overflow-hidden">
      <div className="max-w-6xl mx-auto px-4 text-center">

        {/* TITLE */}
        <p className="text-[#00C2A8] mb-3 text-sm tracking-widest uppercase font-medium">
          ნდობა
        </p>

        <h2 className="text-2xl md:text-3xl font-semibold text-white mb-10">
          გვენდობიან პარტნიორები
        </h2>

        {/* CAROUSEL */}
        <div className="relative w-full overflow-hidden">

          {/* gradient edges */}
          <div className="absolute left-0 top-0 w-24 h-full bg-gradient-to-r from-[#071A2B] to-transparent z-10"></div>
          <div className="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-[#071A2B] to-transparent z-10"></div>

          <div className="flex gap-16 animate-scroll items-center">

            {[...data, ...data].map((logo, i) => {
              if (!logo?.image) return null;

              return (
                <div
                  key={i}
                  className="flex items-center justify-center min-w-[160px] h-[80px]  bg-white/5 backdrop-blur-md border border-white/10  rounded-xl px-4 hover:bg-white/10 transition"
                >
                  <Image
                    src={logo.image}
                    alt="client"
                    width={140}
                    height={60}
                    className="object-contain opacity-80 hover:opacity-100 transition duration-300"
                  />
                </div>
              );
            })}

          </div>
        </div>

      </div>
    </section>
  );
}