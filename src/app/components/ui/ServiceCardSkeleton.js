import { Skeleton } from "./Skeleton";

export default function ServiceCardSkeleton() {
  return (
    <div className="bg-white rounded-2xl p-6 border">
      <Skeleton className="h-40 w-full mb-4" />
      <Skeleton className="h-4 w-3/4 mb-2" />
      <Skeleton className="h-4 w-1/2" />
    </div>
  );
}