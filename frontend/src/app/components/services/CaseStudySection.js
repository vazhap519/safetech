export default function CaseStudySection({ data }) {
  if (!data) return null;

  return (
    <section className="py-24 bg-white">
      <div className="max-w-5xl mx-auto px-6">

        {/* HEADER */}
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900">
            ქეის სტადი
          </h2>

          <p className="mt-4 text-gray-600">
            რეალური მაგალითი ჩვენი მუშაობიდან
          </p>
        </div>

        {/* MAIN CARD */}
        <div className="
          bg-gradient-to-br from-gray-50 to-white
          border border-gray-200
          rounded-3xl
          p-8 md:p-12
          shadow-lg
        ">

          {/* TITLE */}
          <h3 className="text-2xl font-semibold text-gray-900 mb-6 text-center">
            {data.title}
          </h3>

          {/* GRID */}
          <div className="grid md:grid-cols-3 gap-6">

            {/* PROBLEM */}
            <div className="bg-white rounded-2xl p-6 border border-gray-200">
              <p className="text-sm text-gray-500 mb-2">პრობლემა</p>
              <p className="text-gray-700">
                {data.description}
              </p>
            </div>

            {/* SOLUTION */}
            <div className="bg-white rounded-2xl p-6 border border-gray-200">
              <p className="text-sm text-gray-500 mb-2">გადაწყვეტა</p>
              <p className="text-gray-700">
                ჩვენი ოპტიმიზაციის სისტემის დანერგვა
              </p>
            </div>

            {/* RESULT */}
            <div className="
              bg-green-50 
              rounded-2xl 
              p-6 
              border border-green-200
              flex flex-col justify-center items-center text-center
            ">
              <p className="text-sm text-green-700 mb-2">შედეგი</p>

              <p className="text-2xl font-bold text-green-600">
                {data.result}
              </p>
            </div>

          </div>

        </div>

      </div>
    </section>
  );
}