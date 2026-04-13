"use client";
import Link from "next/link";
import useFadeIn from "../../hooks/useFadeIn";
export default function CTASection({data}) {
  
   const [ref, visible] = useFadeIn();
    if (!data) return null;
  return (


<section
  ref={ref}
  className={`py-24 bg-gradient-to-r from-[#00C2A8] to-[#0B3C5D] text-white transition-all duration-700 ${
    visible ? "opacity-100 translate-y-0" : "opacity-0 translate-y-10"
  }`}
>
  <div className="max-w-4xl mx-auto px-6 text-center">

    <h2 className="text-4xl font-bold leading-tight">
      {data.cta_title}
      <br />
      <span className="text-white/80">{data.cta_title_hilight}</span>
    </h2>

    <p className="mt-4 text-white/80">
      {data.cta_description}
    </p>

    <div className="mt-10 flex justify-center gap-4 flex-wrap">

      <a
        href={`tel:${data.cta_phone_btn_number}`}
        className="bg-white text-[#0B3C5D] px-6 py-3 rounded-xl font-medium shadow-lg hover:scale-105 transition"
      >
        📞 {data.cta_phone_btn_text}
      </a>

      <Link
        href="/contact"
        className="border border-white/30 px-6 py-3 rounded-xl hover:bg-white hover:text-[#0B3C5D] transition"
      >
        {data.cta_message_button_text}
      </Link>

    </div>

  </div>
</section>
  );
}