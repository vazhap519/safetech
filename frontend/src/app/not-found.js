import LocalizedLink from "@/components/ui/LocalizedLink";

import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export const metadata = {
  robots: {
    index: false,
    follow: false,
  },
};

export default async function NotFound() {
  const { locale, translations } = await getSiteSettings();
  const title = translateText(translations, "notFound.title", locale, {
    ka: "გვერდი ვერ მოიძებნა",
    en: "Page not found",
    ru: "Страница не найдена",
  });
  const description = translateText(
    translations,
    "notFound.description",
    locale,
    {
      ka: "მითითებული მისამართი არასწორია ან გვერდი გადატანილია.",
      en: "The address may be incorrect or the page may have moved.",
      ru: "Возможно, адрес указан неверно или страница была перемещена.",
    },
  );
  const homeLabel = translateText(translations, "notFound.home", locale, {
    ka: "მთავარ გვერდზე დაბრუნება",
    en: "Return home",
    ru: "Вернуться на главную",
  });
  const contactLabel = translateText(
    translations,
    "notFound.contact",
    locale,
    {
      ka: "დაგვიკავშირდით",
      en: "Contact us",
      ru: "Связаться с нами",
    },
  );

  return (
    <section className="flex min-h-screen items-center justify-center bg-gradient-to-br from-[#071A2B] via-[#0A2238] to-[#071A2B] px-6 text-white">
      <div className="max-w-xl text-center">
        <h1 className="text-7xl font-bold text-[#00E0B8] md:text-9xl">404</h1>

        {title ? (
          <h2 className="mt-6 text-2xl font-semibold md:text-3xl">{title}</h2>
        ) : null}

        {description ? (
          <p className="mt-4 text-white/70">{description}</p>
        ) : null}

        {homeLabel || contactLabel ? (
          <div className="mt-10 flex flex-col justify-center gap-4 sm:flex-row">
            {homeLabel ? (
              <LocalizedLink
                className="rounded-xl bg-[#00E0B8] px-8 py-3 font-semibold text-black transition hover:scale-105"
                href="/"
              >
                {homeLabel}
              </LocalizedLink>
            ) : null}

            {contactLabel ? (
              <LocalizedLink
                className="rounded-xl border border-white/20 px-8 py-3 transition hover:bg-white/10"
                href="/contact"
              >
                {contactLabel}
              </LocalizedLink>
            ) : null}
          </div>
        ) : null}
      </div>
    </section>
  );
}
