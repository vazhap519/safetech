import BlogPage from "@/app/blog/page";

export default function BlogCategoryPage({ params }) {
  return (
    <BlogPage
      searchParams={{
        category: params.slug,
      }}
    />
  );
}