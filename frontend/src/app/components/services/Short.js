export default function Short({ short }) {
  if (!short) return null;

  return (
    <section className="py-12 bg-white">
      <div className="max-w-4xl mx-auto px-4">

        <div className="bg-[#F8FAFC] p-6 md:p-8 rounded-2xl shadow-sm text-center">
          <p className="text-gray-700 text-lg leading-relaxed">
            {short}
          </p>
        </div>

      </div>
    </section>
  );
}