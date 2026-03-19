export default function QuickCTA({ cta }) {
  if (!cta) return null;

  return (
    <section className="py-10 text-center fade-in hover:scale-[1.01] transition-all duration-500">

      <p className="text-slate-400 mb-4">
        {cta.text}
      </p>

      <a
        href={cta.link}
        className="bg-primary text-darkbg px-8 py-3 rounded-md font-semibold hover:scale-105 transition-all duration-300"
      >
        {cta.button}
      </a>

    </section>
  );
}