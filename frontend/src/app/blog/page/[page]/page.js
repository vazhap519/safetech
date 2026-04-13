import BlogPage from "@/app/blog/page";

export default function BlogPaginatedPage({ params, searchParams }) {
  return (
    <BlogPage
      searchParams={{
        ...searchParams,
        page: Number(params.page),
      }}
    />
  );
}