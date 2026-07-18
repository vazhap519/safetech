import { getPrivacy } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";

export default async function PrivacyPage() {
  const locale = await getCurrentLocale();
  const privacy = await getPrivacy({ locale });

  if (!privacy?.title && !privacy?.highlight && !privacy?.content) {
    return null;
  }

  return (
    <main className="bg-white text-gray-800">
      {privacy?.title || privacy?.highlight ? (
        <section className="bg-[#0B3C5D] py-20 text-white">
          <div className="mx-auto max-w-5xl px-4 text-center">
            {privacy?.title ? (
              <h1 className="mb-4 text-3xl font-bold md:text-4xl">
                {privacy.title}
              </h1>
            ) : null}
            {privacy?.highlight ? (
              <p className="mx-auto max-w-2xl text-gray-300">
                {privacy.highlight}
              </p>
            ) : null}
          </div>
        </section>
      ) : null}

      {privacy?.content ? (
        <section className="mx-auto max-w-5xl px-4 py-20">
          <div className="rounded-2xl bg-white p-6 shadow-lg md:p-10">
            <div
              className="prose max-w-none prose-lg prose-h2:mt-10 prose-h2:font-bold prose-h2:text-[#0B3C5D] prose-p:text-gray-600 prose-li:text-gray-600 prose-li:marker:text-[#00C2A8] prose-strong:text-[#0B3C5D] prose-a:text-[#00C2A8] prose-a:no-underline hover:prose-a:underline"
              dangerouslySetInnerHTML={{ __html: privacy.content }}
            />
          </div>
        </section>
      ) : null}
    </main>
  );
}
