"use client";
import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";
export default function CTASection() {
   const [ref, visible] = useFadeIn();
  return (
    <section 
     ref={ref}
  className={`py-20 bg-[#0B3C5D] text-white text-center 
  transition-all duration-700 ease-out will-change-transform ${
    visible
      ? "opacity-100 translate-y-0"
      : "opacity-0 translate-y-10"
  }`}
    
    >
      <div className="max-w-4xl mx-auto px-4">

        {/* Title */}
        <h2 className="text-3xl md:text-4xl font-bold">
          გჭირდება IT სერვისი?
          <br />
          დაგვიკავშირდი ახლავე
        </h2>

        {/* Description */}
        <p className="mt-4 text-gray-300">
          Safetech გთავაზობთ სწრაფ და ხარისხიან მომსახურებას მთელი საქართველოს მასშტაბით.
        </p>

        {/* Buttons */}
        <div className="mt-8 flex flex-wrap justify-center gap-4">

          {/* Primary CTA */}
          <a
            href="tel:+995599000000"
            className="bg-[#00C2A8] text-white px-6 py-3 rounded-xl hover:bg-[#00a892] transition shadow-lg"
          >
            📞 დაგვირეკე
          </a>

          {/* Secondary CTA */}
          <Link
            href="/contact"
            className="border-2 border-white px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition"
          >
            მოგვწერე
          </Link>

        </div>

      </div>
    </section>
  );
}