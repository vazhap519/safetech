// import { notFound } from "next/navigation";
// import { blogPosts } from "@/data/blog";
// import { buildMetadata } from "@/lib/seo";
// import Share from "@/app/components/Share";
// /* =========================
//    META SEO
// ========================= */
// export async function generateMetadata({ params }) {
//   const { slug } = await params;
//   const post = blogPosts.find((p) => p.slug === slug);

//   if (!post) return {};

//   return buildMetadata({
//     title: post.title,
//     description: post.desc,
//     image: post.image,
//     path: `/blog/${slug}`,
//   });
// }

// /* =========================
//    PAGE
// ========================= */
// export default async function BlogDetailPage({ params }) {
//   const { slug } = await params;
//   const post = blogPosts.find((p) => p.slug === slug);

//   if (!post) return notFound();

//   const relatedPosts = blogPosts
//     .filter((p) => p.category === post.category && p.slug !== post.slug)
//     .slice(0, 3);

//   const url = `https://safetech.ge/blog/${slug}`;

//   return (
//     <main className="py-20 bg-[#F8FAFC]">
//       <div className="max-w-6xl mx-auto px-4 grid md:grid-cols-3 gap-10">

//         {/* ================= LEFT ================= */}
//         <article className="md:col-span-2">

//           {/* BREADCRUMBS */}
//           <nav className="text-sm text-gray-400 mb-4">
//             <a href="/" className="hover:text-[#00C2A8]">მთავარი</a> /{" "}
//             <a href="/blog" className="hover:text-[#00C2A8]">ბლოგი</a> /{" "}
//             <span className="text-gray-600">{post.title}</span>
//           </nav>

//           {/* IMAGE */}
//           <div className="overflow-hidden rounded-2xl shadow-lg group">
//             <img
//               src={post.image}
//               alt={post.title}
//               className="w-full h-72 object-cover group-hover:scale-105 transition duration-500"
//             />
//           </div>

//           {/* META */}
//           <div className="mt-6 flex flex-wrap gap-3 text-xs">
//             <span className="bg-gray-100 px-3 py-1 rounded-full">📅 2026</span>
//             <span className="bg-gray-100 px-3 py-1 rounded-full">📖 3 წუთი</span>
//             <span className="bg-[#00C2A8]/10 text-[#00C2A8] px-3 py-1 rounded-full">
//               {post.category}
//             </span>
//           </div>

//           {/* TITLE */}
//           <h1 className="mt-4 text-3xl md:text-4xl font-bold text-[#0B3C5D]">
//             {post.title}
//           </h1>

//           {/* DESC */}
//           <p className="mt-4 text-lg text-gray-600 leading-relaxed">
//             {post.desc}
//           </p>

//           {/* CONTENT */}
//           <div className="mt-10 space-y-6 text-gray-700 text-[17px]">
//             {post.content?.map((p, i) => (
//               <p
//                 key={i}
//                 id={`section-${i}`}
//                 className="bg-white p-5 rounded-xl shadow-sm hover:shadow-md transition"
//               >
//                 {p}
//               </p>
//             ))}
//           </div>

//           {/* AUTHOR */}
//           <div className="mt-12 bg-white p-6 rounded-xl shadow flex items-center gap-4">
//             <img
//               src="/avatar.jpg"
//               alt="author"
//               className="w-14 h-14 rounded-full object-cover"
//             />
//             <div>
//               <p className="font-semibold text-[#0B3C5D]">
//                 Safetech Team
//               </p>
//               <p className="text-sm text-gray-500">
//                 IT სპეციალისტები და უსაფრთხოების ექსპერტები
//               </p>
//             </div>
//           </div>

//           {/* SHARE */}
//    {/* <Share 
//   data={data?.shareSection} 
//   url={`https://safetech.ge/blog/${slug}`}
// /> */}

//           {/* CTA */}
//           <div className="mt-12 bg-gradient-to-r from-[#0B3C5D] to-[#06283D] text-white p-8 rounded-2xl text-center shadow-xl">
//             <h3 className="text-2xl font-semibold">
//               გჭირდება მსგავსი სერვისი?
//             </h3>

//             <p className="mt-2 text-gray-300">
//               დაგვიკავშირდი და მიიღე პროფესიონალური დახმარება
//             </p>

//             <a
//               href="tel:+995599000000"
//               className="inline-block mt-6 bg-[#00C2A8] px-6 py-3 rounded-xl hover:bg-[#00a892] transition"
//             >
//               📞 დაგვირეკე
//             </a>
//           </div>

//           {/* RELATED POSTS */}
//           {relatedPosts.length > 0 && (
//             <div className="mt-16">
//               <h3 className="text-2xl font-bold text-[#0B3C5D] mb-6">
//                 მსგავსი სტატიები
//               </h3>

//               <div className="grid md:grid-cols-3 gap-6">
//                 {relatedPosts.map((item) => (
//                   <a key={item.slug} href={`/blog/${item.slug}`}>
//                     <div className="group bg-white rounded-xl overflow-hidden shadow hover:shadow-xl transition">

//                       <img
//                         src={item.image}
//                         alt={item.title}
//                         className="h-32 w-full object-cover group-hover:scale-110 transition"
//                       />

//                       <div className="p-4">
//                         <h4 className="text-sm font-semibold text-[#0B3C5D] group-hover:text-[#00C2A8]">
//                           {item.title}
//                         </h4>
//                       </div>

//                     </div>
//                   </a>
//                 ))}
//               </div>
//             </div>
//           )}

//         </article>

//         {/* ================= RIGHT SIDEBAR ================= */}
//         <aside className="hidden md:block">

//           <div className="sticky top-24 bg-white p-6 rounded-xl shadow">

//             <h3 className="font-semibold text-[#0B3C5D] mb-4">
//               📑 სარჩევი
//             </h3>

//             <ul className="space-y-3 text-sm">
//               {post.content?.map((_, i) => (
//                 <li key={i}>
//                   <a
//                     href={`#section-${i}`}
//                     className="text-gray-600 hover:text-[#00C2A8]"
//                   >
//                     სექცია {i + 1}
//                   </a>
//                 </li>
//               ))}
//             </ul>

//           </div>

//         </aside>

//       </div>
//     </main>
//   );
// }

import { notFound } from "next/navigation";
import { generateSeo } from "@/lib/seoEngine";
import { getBlogPost } from "@/lib/datafetch";
import { getCurrentUrl } from "@/lib/getUrl";

const DEFAULT_IMAGE = "/images/blog-placeholder.webp";

/* =========================
   META SEO
========================= */
export async function generateMetadata({ params }) {
  const { slug } = await params; // 🔥 FIX
  const url = getCurrentUrl(`/blog/${slug}`);

  if (!slug) return {};

  return generateSeo({
    type: "blog",
    slug,
  });
}

/* =========================
   PAGE
========================= */
export default async function BlogDetailPage({ params }) {
  const { slug } = await params; // 🔥 FIX

  if (!slug) return notFound();

  const res = await getBlogPost(slug);
  const post = res?.data;

  if (!post) return notFound();

  return (
    <main className="py-20 bg-[#F8FAFC]">
      <div className="max-w-6xl mx-auto px-4 grid md:grid-cols-3 gap-10">

        {/* ================= LEFT ================= */}
        <article className="md:col-span-2">

          {/* BREADCRUMBS */}
          <nav className="text-sm text-gray-400 mb-4">
            <a href="/" className="hover:text-[#00C2A8]">მთავარი</a> /{" "}
            <a href="/blog" className="hover:text-[#00C2A8]">ბლოგი</a> /{" "}
            <span className="text-gray-600">{post.title}</span>
          </nav>

          {/* IMAGE */}
          <div className="overflow-hidden rounded-2xl shadow-lg group">
            <img
              src={post.image || DEFAULT_IMAGE} // 🔥 FIX
              alt={post.title}
              className="w-full h-72 object-cover group-hover:scale-105 transition"
            />
          </div>

          {/* META */}
          <div className="mt-6 flex flex-wrap gap-3 text-xs">
            <span className="bg-gray-100 px-3 py-1 rounded-full">
              📅 {post.published_year}
            </span>
            <span className="bg-gray-100 px-3 py-1 rounded-full">
              📖 {post.reading_time} წუთი
            </span>
            {post.category && (
              <span className="bg-[#00C2A8]/10 text-[#00C2A8] px-3 py-1 rounded-full">
                {post.category.name}
              </span>
            )}
          </div>

          {/* TITLE */}
          <h1 className="mt-4 text-3xl md:text-4xl font-bold text-[#0B3C5D]">
            {post.title}
          </h1>

          {/* DESC */}
          <p className="mt-4 text-lg text-gray-600 leading-relaxed">
            {post.excerpt}
          </p>
      <Share data={res?.share ?? []} url={url} />
          {/* CONTENT */}
          <div className="mt-10 space-y-6 text-gray-700 text-[17px]">
            {post.sections?.map((section, i) => (
              <div
                key={section.id}
                id={`section-${i}`}
                className="bg-white p-5 rounded-xl shadow-sm"
                dangerouslySetInnerHTML={{ __html: section.content }}
              />
            ))}
          </div>

          {/* AUTHOR */}
          {post.author && (
            <div className="mt-12 bg-white p-6 rounded-xl shadow flex items-center gap-4">
              <img
                src={post.author.avatar || DEFAULT_IMAGE} // 🔥 FIX
                alt={post.author.name}
                className="w-14 h-14 rounded-full object-cover"
              />
              <div>
                <p className="font-semibold text-[#0B3C5D]">
                  {post.author.name}
                </p>
              </div>
            </div>
          )}

          {/* RELATED */}
          {post.related?.length > 0 && (
            <div className="mt-16">
              <h3 className="text-2xl font-bold text-[#0B3C5D] mb-6">
                მსგავსი სტატიები
              </h3>

              <div className="grid md:grid-cols-3 gap-6">
                {post.related.map((item) => (
                  <a key={item.slug} href={`/blog/${item.slug}`}>
                    <div className="bg-white rounded-xl overflow-hidden shadow hover:shadow-xl transition">

                      <img
                        src={item.image || DEFAULT_IMAGE} // 🔥 FIX
                        alt={item.title}
                        className="h-32 w-full object-cover"
                      />

                      <div className="p-4">
                        <h4 className="text-sm font-semibold text-[#0B3C5D]">
                          {item.title}
                        </h4>
                      </div>

                    </div>
                  </a>
                ))}
              </div>
            </div>
          )}

        </article>

        {/* ================= SIDEBAR ================= */}
        <aside className="hidden md:block">
          <div className="sticky top-24 bg-white p-6 rounded-xl shadow">

            <h3 className="font-semibold text-[#0B3C5D] mb-4">
              📑 სარჩევი
            </h3>

            <ul className="space-y-3 text-sm">
              {post.sections?.map((_, i) => (
                <li key={i}>
                  <a
                    href={`#section-${i}`}
                    className="text-gray-600 hover:text-[#00C2A8]"
                  >
                    სექცია {i + 1}
                  </a>
                </li>
              ))}
            </ul>

          </div>
        </aside>

      </div>
    </main>
  );
}