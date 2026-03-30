import Link from "next/link";

export default function NotFound() {
  return (
    <section className="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B] text-white px-6">

      <div className="text-center max-w-xl">

        <h1 className="text-7xl md:text-9xl font-bold text-[#00E0B8]">
          404
        </h1>

        <h2 className="mt-6 text-2xl md:text-3xl font-semibold">
          გვერდი ვერ მოიძებნა
        </h2>

        <p className="mt-4 text-white/70">
          შესაძლოა ბმული არასწორია ან გვერდი წაშლილია
        </p>

        <div className="mt-10 flex flex-col sm:flex-row gap-4 justify-center">

          <Link
            href="/"
            className="bg-[#00E0B8] text-black px-8 py-3 rounded-xl font-semibold hover:scale-105 transition"
          >
            მთავარ გვერდზე
          </Link>

          <Link
            href="/contact"
            className="border border-white/20 px-8 py-3 rounded-xl hover:bg-white/10 transition"
          >
            დაგვიკავშირდი
          </Link>

        </div>

      </div>

    </section>
  );
}