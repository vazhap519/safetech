import BlogPage from "@/app/blog/page";

export default async function BlogCategoryPaginatedPage({ params }) {
  const { slug, page } = await params;

  return (
    <BlogPage
      searchParams={{
        category: slug,
        page: Number(page),
      }}
    />
  );
}
