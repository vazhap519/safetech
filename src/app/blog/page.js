import BlogList from "@/app/components/blog/BlogList";

const BASE_URL = process.env.NEXT_PUBLIC_SITE_URL;
import { buildMetadata } from "@/lib/seo";
import { getSeoByKey } from "@/lib/datafetch";
import EmptyState from "../components/ui/EmptyState";
import { getEmpty } from "@/lib/datafetch";
import { getBlog } from "@/lib/datafetch";

/* =========================
   SEO (BLOG 🔥)
========================= */
export async function generateMetadata() {
  const seo = await getSeoByKey("blog");
  const data = seo?.data;

  return buildMetadata({
    title: data?.title,
    description: data?.description,
    image: data?.og?.image,
    keywords: data?.keywords,
    canonical: data?.canonical,
    noindex: data?.noindex,
    og: data?.og,
    path: data?.slug || "/blog",
  });
}

/* =========================
   PAGE
========================= */


// export default async function BlogPage({ searchParams }) {
//   const params = await searchParams;
//   const page = Number(params?.page || 1);
//   const category = params?.category || "all";

//   /* 🔥 მთავარი fetch */
//   const res = await getBlog({ page, category });

//   const empty=await getEmpty();
  
//   if(!res||res.error){
//     return(
// <EmptyState empty={empty}/>
//     )
    
//   }

//   return (
//     <main className="py-20 bg-[#F8FAFC]">
//       <div className="max-w-6xl mx-auto px-4">
//         <h1 className="text-3xl font-bold text-[#0B3C5D] text-center">
//           ბლოგი
//         </h1>

//         <BlogList page={page} category={category} />
//       </div>
//     </main>
//   )
// }

export default async function BlogPage({ searchParams }) {
  const params = await searchParams;
  const page = Number(params?.page || 1);
  const category = params?.category || "all";

  const [res, emptyRes] = await Promise.all([
    getBlog({ page, category }),
    getEmpty().catch(() => null),
  ]);

const meta = res?.meta ?? {};
  const empty = emptyRes?.data || emptyRes || null;

  /* ❌ backend down */
  if (!res || res.error) {
    return <EmptyState empty={empty} />;
  }

  const posts = res?.data ?? []; // ✅ სწორია

  /* 📭 empty data */
  if (posts.length === 0) {
    return <EmptyState empty={empty} />;
  }

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4">

        <h1 className="text-3xl font-bold text-[#0B3C5D] text-center mb-10">
          ბლოგი
        </h1>

       <BlogList 
 posts={posts}
  meta={meta}
  page={page}
  category={category}
/>

      </div>
    </main>
  );
}