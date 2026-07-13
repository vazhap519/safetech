import Link from "next/link";

import { getPrivacy } from "@/lib/datafetch";
import { getCurrentLocale } from "@/lib/locale-server";

export default async function PrivacyPage() {
  const locale = await getCurrentLocale();
  const copy = {
    ka: {
      title: "კონფიდენციალურობის პოლიტიკა",
      empty: "კონტენტი ჯერ არ არის დამატებული",
      back: "← მთავარ გვერდზე დაბრუნება",
    },
    en: {
      title: "Privacy Policy",
      empty: "No content has been added yet",
      back: "← Back to home",
    },
    ru: {
      title: "Политика конфиденциальности",
      empty: "Контент пока не добавлен",
      back: "← Вернуться на главную",
    },
  }[locale];
  const privacy = await getPrivacy({ locale });

  return (
    <main className="bg-white text-gray-800">
      <section className="bg-[#0B3C5D] py-20 text-white">
        <div className="mx-auto max-w-5xl px-4 text-center">
          <h1 className="mb-4 text-3xl font-bold md:text-4xl">
            {privacy?.title || copy.title}
          </h1>
          <p className="mx-auto max-w-2xl text-gray-300">
            {privacy?.highlight || copy.title}
          </p>
        </div>
      </section>

      <section className="mx-auto max-w-5xl px-4 py-20">
        <div className="rounded-2xl bg-white p-6 shadow-lg md:p-10">
          <div
            className="prose max-w-none prose-lg prose-h2:mt-10 prose-h2:font-bold prose-h2:text-[#0B3C5D] prose-p:text-gray-600 prose-li:text-gray-600 prose-li:marker:text-[#00C2A8] prose-strong:text-[#0B3C5D] prose-a:text-[#00C2A8] prose-a:no-underline hover:prose-a:underline"
            dangerouslySetInnerHTML={{
              __html: privacy?.content || `<p>${copy.empty}</p>`,
            }}
          />
        </div>

        <div className="pt-12 text-center">
          <Link
            href="/"
            className="inline-flex items-center gap-2 rounded-xl bg-[#00C2A8] px-6 py-3 text-white shadow transition hover:scale-105"
          >
            {copy.back}
          </Link>
        </div>
      </section>
    </main>
  );
}
