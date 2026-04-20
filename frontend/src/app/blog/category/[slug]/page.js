import BlogPage from "@/app/blog/page";
import { getBaseUrl } from "@/lib/config";
import { getCategories } from "@/lib/datafetch";

async function getCategoryName(slug) {
  try {
    const res = await getCategories();
    const categories = Array.isArray(res) ? res : res?.data || [];
    return categories.find((cat) => cat.slug === slug)?.name || slug;
  } catch {
    return slug;
  }
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const name = await getCategoryName(slug);
  const url = `${getBaseUrl()}/blog/category/${slug}`;

  return {
    title: `${name} ბლოგი | Safetech`,
    description: `${name} თემაზე Safetech-ის სტატიები, რჩევები და პრაქტიკული ინფორმაცია.`,
    alternates: {
      canonical: url,
    },
    robots: {
      index: true,
      follow: true,
    },
    openGraph: {
      title: `${name} ბლოგი | Safetech`,
      description: `${name} თემაზე Safetech-ის სტატიები, რჩევები და პრაქტიკული ინფორმაცია.`,
      url,
      type: "website",
      locale: "ka_GE",
      siteName: "Safetech",
    },
  };
}

export default async function BlogCategoryPage({ params }) {
  const { slug } = await params;

  return (
    <BlogPage
      searchParams={{
        category: slug,
      }}
    />
  );
}
