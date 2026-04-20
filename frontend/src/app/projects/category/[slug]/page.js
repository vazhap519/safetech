import ProjectsPage from "@/app/projects/page";
import { getBaseUrl } from "@/lib/config";
import { getProjectCategories } from "@/lib/datafetch";

async function getCategoryName(slug) {
  try {
    const res = await getProjectCategories();
    const categories = Array.isArray(res) ? res : res?.data || [];
    return categories.find((cat) => cat.slug === slug)?.name || slug;
  } catch {
    return slug;
  }
}

export async function generateMetadata({ params }) {
  const { slug } = await params;
  const name = await getCategoryName(slug);
  const url = `${getBaseUrl()}/projects/category/${slug}`;

  return {
    title: `${name} პროექტები | Safetech`,
    description: `${name} მიმართულების შესრულებული პროექტები Safetech-ისგან.`,
    alternates: {
      canonical: url,
    },
    robots: {
      index: true,
      follow: true,
    },
    openGraph: {
      title: `${name} პროექტები | Safetech`,
      description: `${name} მიმართულების შესრულებული პროექტები Safetech-ისგან.`,
      url,
      type: "website",
      locale: "ka_GE",
      siteName: "Safetech",
    },
  };
}

export default async function ProjectCategoryPage({ params }) {
  const { slug } = await params;

  return <ProjectsPage searchParams={{ category: slug }} />;
}
