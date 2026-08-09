import LocalizedLink from "@/components/ui/LocalizedLink";

import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export const metadata = {
  robots: {
    index: false,
    follow: false,
  },
};

const fallbackCopy = {
  title: {
    ka: "გვერდი ვერ მოიძებნა",
    en: "Page not found",
    ru: "Страница не найдена",
  },
  description: {
    ka: "მითითებული გვერდი არ არსებობს ან მისამართი შეიცვალა.",
    en: "The requested page does not exist or its address has changed.",
    ru: "Запрошенная страница не существует или её адрес изменился.",
  },
  home: {
    ka: "მთავარ გვერდზე დაბრუნება",
    en: "Return home",
    ru: "На главную",
  },
  contact: {
    ka: "დაგვიკავშირდით",
    en: "Contact us",
    ru: "Связаться с нами",
  },
};

export default async function NotFound() {
  const { locale, translations } = await getSiteSettings();
  const title = translateText(
    translations,
    "notFound.title",
    locale,
    fallbackCopy.title,
  );
  const description = translateText(
    translations,
    "notFound.description",
    locale,
    fallbackCopy.description,
  );
  const homeLabel = translateText(
    translations,
    "notFound.home",
    locale,
    fallbackCopy.home,
  );
  const contactLabel = translateText(
    translations,
    "notFound.contact",
    locale,
    fallbackCopy.contact,
  );

  return (
    <section className="flex min-h-[70svh] items-center justify-center bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B] px-5 py-20 text-white sm:px-6">
      <div className="w-full max-w-xl text-center">
        <h1 className="text-7xl font-bold text-[#00E0B8] md:text-9xl">404</h1>

        <h2 className="mt-6 text-2xl font-semibold md:text-3xl">{title}</h2>
        <p className="mx-auto mt-4 max-w-lg text-white/70">{description}</p>

        <div className="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
          <LocalizedLink
            className="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#00E0B8] px-8 py-3 font-semibold text-black transition hover:scale-[1.02]"
            href="/"
          >
            {homeLabel}
          </LocalizedLink>

          <LocalizedLink
            className="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/20 px-8 py-3 transition hover:bg-white/10"
            href="/contact"
          >
            {contactLabel}
          </LocalizedLink>
        </div>
      </div>
    </section>
  );
}
