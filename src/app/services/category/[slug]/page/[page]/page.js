import ServicesPage from "@/app/services/page";

/* =========================
   🔥 SEO (CRITICAL)
========================= */
export async function generateMetadata({ params }) {
  const { slug, page } = params;

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;

  let url = `${BASE_URL}/services/category/${slug}`;

  if (page > 1) {
    url += `/page/${page}`;
  }

  // ❌ noindex deep pages
  const noindex = Number(page) > 5;

  return {
    alternates: {
      canonical: url,
    },
    robots: {
      index: !noindex,
      follow: true,
    },
  };
}

/* =========================
   PAGE
========================= */
export default function CategoryPaginatedPage({ params }) {
  const { slug, page } = params;

  return (
    <ServicesPage
      searchParams={{
        category: slug,
        page: Number(page),
      }}
    />
  );
}