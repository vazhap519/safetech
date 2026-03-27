import Image from "next/image";
export default function ServiceHero({ service }) {
  if (!service) return null;

  return (
    <section className="bg-[#0B3C5D] text-white py-20">
      <div className="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-10 items-center">

        <div>
          <h1 className="text-4xl md:text-5xl font-bold">
            {service.title}
          </h1>

          <p className="mt-4 text-gray-300 text-lg">
            {service.description}
          </p>

          <a
            href={`tel:${service.phone || "+995599000000"}`}
            className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl"
          >
            {service.button_text || "📞 დაგვირეკე"}
          </a>
        </div>

        <div className="relative w-full h-[300px] md:h-[400px]">
          <Image
            src={service.image || "/placeholder.jpg"}
            alt={service.title}
            fill
            className="object-cover rounded-2xl"
            priority
          />
        </div>

      </div>
    </section>
  );
}