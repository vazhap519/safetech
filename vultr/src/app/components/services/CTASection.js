export default function CTASection({ service }) {
  return (
    <section className="
      relative 
      py-24 
      bg-gradient-to-br 
      from-[#071A2B] 
      via-[#0A2238] 
      to-[#071A2B]
      text-white 
      text-center 
      overflow-hidden
    ">

      {/* 🔥 GLOW BACKGROUND */}
      <div className="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(0,224,184,0.25),transparent_60%)] pointer-events-none" />

      <div className="relative z-10 max-w-3xl mx-auto px-6">

        {/* TITLE */}
        <h2 className="text-4xl md:text-5xl font-bold leading-tight">
          {service?.cta?.title || "მზად ხარ შედეგებისთვის?"}
        </h2>

        {/* DESCRIPTION */}
        <p className="mt-6 text-lg text-white/80 max-w-xl mx-auto">
          {service?.cta?.description || "მიიღე უფასო კონსულტაცია და გაიგე როგორ შეგიძლია გააუმჯობესო შენი სისტემა"}
        </p>

        {/* CTA BUTTON */}
        <div className="mt-10 flex flex-col sm:flex-row justify-center gap-4">

          <a
            href={`tel:${service?.phone}`}
            className="
              bg-[#00E0B8]
              text-black
              px-10 py-5
              rounded-2xl
              font-semibold
              text-lg
              shadow-[0_10px_40px_rgba(0,224,184,0.4)]
              hover:scale-105
              transition-all duration-300
            "
          >
            📞 {service?.button_text || "დაგვიკავშირდი ახლა"}
          </a>

          {/* SECONDARY TRUST */}
          <div className="text-sm text-white/60 self-center">
            ⚡ პასუხი 5 წუთში
          </div>

        </div>

      </div>
    </section>
  );
}