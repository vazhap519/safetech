import ServicesPage from "@/app/services/page";

export default async function PaginatedPage({ params, searchParams }) {
  const page = Number(params.page) || 1;

  return (
    <ServicesPage
      searchParams={{
        ...searchParams,
        page,
      }}
    />
  );
}