import ConsultationTrigger from "@/components/consultation/ConsultationTrigger";
import JsonLd from "@/components/seo/JsonLd";
import Icon from "@/components/ui/Icon";
import Image from "@/components/ui/Image";
import LocalizedLink from "@/components/ui/LocalizedLink";
import ServiceViewTracker from "@/features/service-detail/components/ServiceViewTracker";
import type { LocalServiceLanding } from "@/lib/local-service-landings";
import type { Locale } from "@/lib/locales";
import { absoluteLocalizedUrl, absoluteSiteUrl } from "@/lib/seo";

const labels = {
    ka: {
        service: "სერვისი",
        benefits: "რას მიიღებთ",
        details: "მომსახურება ადგილობრივად",
        proof: "რეალური პროექტები",
        proofText: "ნახეთ შესაბამისი სამუშაოები, რომლებიც SafeTech-მა რეალურ ობიექტებზე შეასრულა.",
        faq: "ხშირად დასმული კითხვები",
        consultation: "კონსულტაციის მოთხოვნა",
        quote: "შეთავაზების მიღება",
        servicePage: "სერვისის სრული აღწერა",
        defaultCtaTitle: "გჭირდებათ ზუსტი შეთავაზება?",
        defaultCtaText: "მოგვწერეთ ობიექტის დეტალები და დაგეხმარებით შესაბამისი გადაწყვეტის, სამუშაოს მოცულობისა და შემდეგი ნაბიჯების განსაზღვრაში.",
    },
    en: {
        service: "Service",
        benefits: "What you get",
        details: "Local service details",
        proof: "Real projects",
        proofText: "See relevant work SafeTech has completed on real properties.",
        faq: "Frequently asked questions",
        consultation: "Request consultation",
        quote: "Get a quote",
        servicePage: "Full service details",
        defaultCtaTitle: "Need an accurate proposal?",
        defaultCtaText: "Send us the property details and we will help define the right solution, scope and next steps.",
    },
    ru: {
        service: "Услуга",
        benefits: "Что вы получите",
        details: "Услуга в вашем регионе",
        proof: "Реальные проекты",
        proofText: "Посмотрите соответствующие работы SafeTech на реальных объектах.",
        faq: "Частые вопросы",
        consultation: "Запросить консультацию",
        quote: "Получить предложение",
        servicePage: "Полное описание услуги",
        defaultCtaTitle: "Нужно точное предложение?",
        defaultCtaText: "Отправьте данные объекта, и мы поможем определить подходящее решение, объем работ и следующие шаги.",
    },
} satisfies Record<Locale, Record<string, string>>;

export default function LocalServiceLandingView({
    landing,
    locale,
}: {
    landing: LocalServiceLanding;
    locale: Locale;
}) {
    const copy = labels[locale];
    const pagePath = `/services/${landing.service.slug}/${landing.locationSlug}`;
    const pageUrl = absoluteLocalizedUrl(pagePath, locale);
    const organizationId = `${absoluteSiteUrl("/")}#organization`;
    const paragraphs = landing.content
        .split(/\n\s*\n/)
        .map((paragraph) => paragraph.trim())
        .filter(Boolean);
    const faqs = landing.faqs.filter(
        (faq) => faq.question?.trim() && faq.answer?.trim(),
    );
    const schemas = [
        {
            "@context": "https://schema.org",
            "@type": "Service",
            "@id": `${pageUrl}#service`,
            name: landing.title,
            serviceType: landing.service.name || landing.service.title,
            description:
                landing.seo?.description || landing.excerpt || landing.content,
            url: pageUrl,
            areaServed: {
                "@type": "City",
                name: landing.locationName,
            },
            provider: {
                "@id": organizationId,
            },
        },
        {
            "@context": "https://schema.org",
            "@type": "BreadcrumbList",
            itemListElement: [
                {
                    "@type": "ListItem",
                    position: 1,
                    name: "SafeTech",
                    item: absoluteLocalizedUrl("/", locale),
                },
                {
                    "@type": "ListItem",
                    position: 2,
                    name: copy.service,
                    item: absoluteLocalizedUrl("/services", locale),
                },
                {
                    "@type": "ListItem",
                    position: 3,
                    name: landing.service.name,
                    item: absoluteLocalizedUrl(
                        `/services/${landing.service.slug}`,
                        locale,
                    ),
                },
                {
                    "@type": "ListItem",
                    position: 4,
                    name: landing.locationName,
                    item: pageUrl,
                },
            ],
        },
        ...(faqs.length
            ? [
                  {
                      "@context": "https://schema.org",
                      "@type": "FAQPage",
                      mainEntity: faqs.map((faq) => ({
                          "@type": "Question",
                          name: faq.question,
                          acceptedAnswer: {
                              "@type": "Answer",
                              text: faq.answer,
                          },
                      })),
                  },
              ]
            : []),
    ];

    return (
        <article className="pt-[76px]">
            <ServiceViewTracker serviceSlug={landing.service.slug} />
            <JsonLd data={schemas} />

            <section className="relative overflow-hidden px-5 py-14 md:px-8 md:py-20 xl:px-14">
                <div aria-hidden="true" className="ambient-glow -left-24 -top-24" />
                <div className="mx-auto grid w-full max-w-container-max items-center gap-10 lg:grid-cols-[minmax(0,1.05fr)_minmax(380px,0.95fr)] lg:gap-14">
                    <div>
                        <nav className="mb-6 flex flex-wrap items-center gap-2 text-sm text-on-surface-variant">
                            <LocalizedLink className="hover:text-primary" href="/services">
                                {copy.service}
                            </LocalizedLink>
                            <span aria-hidden="true">/</span>
                            <LocalizedLink
                                className="hover:text-primary"
                                href={`/services/${landing.service.slug}`}
                            >
                                {landing.service.name}
                            </LocalizedLink>
                            <span aria-hidden="true">/</span>
                            <span>{landing.locationName}</span>
                        </nav>

                        {landing.eyebrow ? (
                            <p className="mb-5 inline-flex rounded-full border border-primary/20 bg-primary/10 px-4 py-2 text-sm font-medium text-primary">
                                {landing.eyebrow}
                            </p>
                        ) : null}
                        <h1 className="max-w-4xl text-[34px] font-semibold leading-[1.14] text-white sm:text-[42px] md:text-[50px] xl:text-[56px]">
                            {landing.title}
                        </h1>
                        {landing.excerpt ? (
                            <p className="mt-6 max-w-3xl text-base leading-8 text-on-surface-variant md:text-lg">
                                {landing.excerpt}
                            </p>
                        ) : null}

                        <div className="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                            <ConsultationTrigger className="glow-button rounded-xl bg-primary-container px-6 py-4 text-center font-semibold text-on-primary-container">
                                {copy.consultation}
                            </ConsultationTrigger>
                            <LocalizedLink
                                className="rounded-xl border border-outline-variant bg-white/5 px-6 py-4 text-center font-semibold text-white transition hover:bg-white/10"
                                href="/contact#contact-form"
                            >
                                {copy.quote}
                            </LocalizedLink>
                            <LocalizedLink
                                className="rounded-xl px-5 py-4 text-center font-medium text-primary hover:underline"
                                href={`/services/${landing.service.slug}`}
                            >
                                {copy.servicePage}
                            </LocalizedLink>
                        </div>
                    </div>

                    {landing.service.heroImage ? (
                        <div className="relative mx-auto w-full max-w-[620px]">
                            <div aria-hidden="true" className="absolute inset-8 rounded-full bg-primary/15 blur-[100px]" />
                            <div className="relative aspect-[14/9] overflow-hidden rounded-3xl border border-white/10 bg-surface-container-lowest/70 p-1 shadow-2xl">
                                <Image
                                    alt={landing.title}
                                    className="h-full w-full rounded-[1.35rem] object-cover"
                                    height={900}
                                    priority
                                    sizes="(max-width: 1023px) 100vw, 45vw"
                                    src={landing.service.heroImage}
                                    width={1400}
                                />
                            </div>
                        </div>
                    ) : null}
                </div>
            </section>

            {landing.benefits.length ? (
                <section className="border-y border-white/10 bg-surface-container-low/45 px-5 py-14 md:px-8 xl:px-14">
                    <div className="mx-auto max-w-container-max">
                        <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                            {copy.benefits}
                        </h2>
                        <div className="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            {landing.benefits.map((benefit, index) => (
                                <div
                                    className="rounded-2xl border border-white/10 bg-surface-container p-6"
                                    key={`${benefit.title ?? "benefit"}-${index}`}
                                >
                                    <Icon className="text-primary" name="check_circle" />
                                    {benefit.title ? (
                                        <h3 className="mt-4 text-lg font-semibold text-on-surface">
                                            {benefit.title}
                                        </h3>
                                    ) : null}
                                    {benefit.description ? (
                                        <p className="mt-3 leading-7 text-on-surface-variant">
                                            {benefit.description}
                                        </p>
                                    ) : null}
                                </div>
                            ))}
                        </div>
                    </div>
                </section>
            ) : null}

            <section className="px-5 py-16 md:px-8 xl:px-14">
                <div className="mx-auto max-w-4xl">
                    <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                        {copy.details}: {landing.locationName}
                    </h2>
                    <div className="mt-7 space-y-6 text-base leading-8 text-on-surface-variant md:text-lg">
                        {paragraphs.map((paragraph, index) => (
                            <p key={`${index}-${paragraph.slice(0, 36)}`}>
                                {paragraph}
                            </p>
                        ))}
                    </div>
                </div>
            </section>

            {landing.projects.length ? (
                <section className="bg-surface-container-low/45 px-5 py-16 md:px-8 xl:px-14">
                    <div className="mx-auto max-w-container-max">
                        <div className="max-w-3xl">
                            <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                                {copy.proof}
                            </h2>
                            <p className="mt-3 leading-7 text-on-surface-variant">
                                {copy.proofText}
                            </p>
                        </div>
                        <div className="mt-9 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                            {landing.projects.map((project) => (
                                <LocalizedLink
                                    className="group overflow-hidden rounded-2xl border border-white/10 bg-surface-container transition hover:-translate-y-1 hover:border-primary/40"
                                    href={`/projects/${project.slug}`}
                                    key={project.slug}
                                >
                                    {project.image ? (
                                        <div className="aspect-[3/2] overflow-hidden bg-surface-container-high">
                                            <Image
                                                alt={project.title}
                                                className="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                                                height={480}
                                                sizes="(max-width: 767px) 100vw, 33vw"
                                                src={project.image}
                                                width={720}
                                            />
                                        </div>
                                    ) : null}
                                    <div className="p-6">
                                        <h3 className="text-lg font-semibold text-on-surface group-hover:text-primary">
                                            {project.title}
                                        </h3>
                                        {project.description ? (
                                            <p className="mt-3 line-clamp-3 leading-7 text-on-surface-variant">
                                                {project.description}
                                            </p>
                                        ) : null}
                                    </div>
                                </LocalizedLink>
                            ))}
                        </div>
                    </div>
                </section>
            ) : null}

            {faqs.length ? (
                <section className="px-5 py-16 md:px-8 xl:px-14">
                    <div className="mx-auto max-w-4xl">
                        <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                            {copy.faq}
                        </h2>
                        <div className="mt-8 space-y-4">
                            {faqs.map((faq, index) => (
                                <details
                                    className="rounded-2xl border border-white/10 bg-surface-container px-5 py-4 open:border-primary/30"
                                    key={`${faq.question}-${index}`}
                                >
                                    <summary className="cursor-pointer font-semibold text-on-surface">
                                        {faq.question}
                                    </summary>
                                    <p className="mt-4 leading-7 text-on-surface-variant">
                                        {faq.answer}
                                    </p>
                                </details>
                            ))}
                        </div>
                    </div>
                </section>
            ) : null}

            <section className="px-5 pb-20 pt-4 md:px-8 xl:px-14">
                <div className="mx-auto max-w-5xl rounded-3xl border border-primary/20 bg-primary/10 p-7 text-center sm:p-10">
                    <h2 className="text-2xl font-semibold text-on-surface md:text-3xl">
                        {landing.ctaTitle || copy.defaultCtaTitle}
                    </h2>
                    <p className="mx-auto mt-4 max-w-3xl leading-7 text-on-surface-variant">
                        {landing.ctaText || copy.defaultCtaText}
                    </p>
                    <div className="mt-7 flex flex-col justify-center gap-3 sm:flex-row">
                        <ConsultationTrigger className="rounded-xl bg-primary-container px-7 py-4 font-semibold text-on-primary-container">
                            {copy.consultation}
                        </ConsultationTrigger>
                        <LocalizedLink
                            className="rounded-xl border border-outline-variant bg-surface/40 px-7 py-4 font-semibold text-on-surface"
                            href="/contact#contact-form"
                        >
                            {copy.quote}
                        </LocalizedLink>
                    </div>
                </div>
            </section>
        </article>
    );
}
