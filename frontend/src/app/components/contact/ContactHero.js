export default function ContactHero({ data }) {
  return (
    <section className="py-20 bg-[#0B3C5D] text-white text-center">
      <h1 className="text-4xl md:text-5xl font-bold">
        {data?.hero_title || "დაგვიკავშირდი"}
      </h1>

      <p className="mt-4 text-gray-300">
        {data?.hero_text || ""}
      </p>
    </section>
  );
}