import ServiceCardSkeleton from "../components/ui/ServiceCardSkeleton";

export default function Loading() {
  return (
    <section className="min-h-screen px-6 py-16">
      <div className="max-w-6xl mx-auto">

        {/* Header Skeleton */}
        <div className="mb-10">
          <div className="h-8 w-48 bg-gray-200 rounded animate-pulse mb-4" />
          <div className="h-4 w-72 bg-gray-200 rounded animate-pulse" />
        </div>

        {/* Cards Skeleton */}
        <div className="grid md:grid-cols-3 gap-6">
          {Array.from({ length: 6 }).map((_, i) => (
            <ServiceCardSkeleton key={i} />
          ))}
        </div>

      </div>
    </section>
  );
}