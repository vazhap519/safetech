import { getBaseUrl } from "@/lib/config";

export async function generateSeo({ type, slug }) {
  const baseUrl = getBaseUrl();

  try {
    let data;

    switch (type) {
      case "blog":
        data = await fetch(
          `${process.env.NEXT_PUBLIC_API_URL}/blog/${slug}`
        ).then((res) => res.json());
        break;

      case "service":
        data = await fetch(
          `${process.env.NEXT_PUBLIC_API_URL}/services/${slug}`
        ).then((res) => res.json());
        break;

      default:
        return {};
    }

    const item = data?.data;

    return {
      title: item?.title,
      description: item?.excerpt,
      openGraph: {
        title: item?.title,
        description: item?.excerpt,
        url: `${baseUrl}/${type}/${slug}`,
        images: [item?.image],
      },
      alternates: {
        canonical: `${baseUrl}/${type}/${slug}`,
      },
    };
  } catch (e) {
    return {};
  }
}