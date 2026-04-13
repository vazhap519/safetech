import BlogPage from "@/app/blog/page";

export default function BlogCategoryPaginatedPage({ params }) {
  return (
    <BlogPage
      searchParams={{
        category: params.slug,
        page: Number(params.page),
      }}
    />
  );
}