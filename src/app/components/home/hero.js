import Image from "next/image";

export default function Hero({ hero }) {
  return (
    <section className="py-24 fade-in hover:scale-[1.01] transition-all duration-500">

      <div className="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 px-6 items-center">

        <div>

          <p className="text-primary mb-3">
            {hero?.badge}
          </p>

          <h1 className="text-5xl font-extrabold mb-6 leading-tight">
            {hero?.title}
          </h1>

          <p className="text-slate-400 mb-8">
            {hero?.subtitle}
          </p>

          <div className="flex gap-4 flex-wrap">

            <a
              href={hero?.primary_link}
              className="bg-primary text-darkbg px-6 py-3 rounded-md font-semibold hover:scale-105 transition"
            >
              {hero?.primary_text}
            </a>

            <a
              href={hero?.secondary_link}
              className="border border-primary px-6 py-3 rounded-md hover:scale-105 transition"
            >
              {hero?.secondary_text}
            </a>

          </div>

        </div>

        {/* IMAGE */}
        {hero?.image && (
          <Image
            src={hero.image}
            width={600}
            height={400}
            className="rounded-xl"
            alt="Hero"
          />
        )}

      </div>

    </section>
  );
}