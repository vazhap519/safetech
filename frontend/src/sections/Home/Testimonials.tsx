import Image from "@/components/ui/Image";
import Typography from "@/components/ui/Typography";
import { getBackendContent, resolveBackendAsset } from "@/lib/backend";
import { getSiteSettings } from "@/lib/site-settings";
import { translateText } from "@/lib/translations";

export default async function Testimonials() {
    const [content, { integrations, locale, translations }] = await Promise.all([
        getBackendContent(),
        getSiteSettings(),
    ]);
    const testimonials = (content.testimonials ?? [])
        .filter((testimonial) => testimonial.quote && testimonial.author)
        .slice(0, 3);

    if (!testimonials.length) return null;

    const eyebrow = translateText(
        translations,
        "home.testimonials.eyebrow",
        locale,
        {
            ka: "კლიენტების გამოცდილება",
            en: "Client feedback",
            ru: "Отзывы клиентов",
        },
    );
    const title = translateText(
        translations,
        "home.testimonials.title",
        locale,
        {
            ka: "რეალური შეფასებები შესრულებული სამუშაოს შემდეგ",
            en: "Real feedback after completed work",
            ru: "Реальные отзывы после выполненных работ",
        },
    );
    const description = translateText(
        translations,
        "home.testimonials.description",
        locale,
        {
            ka: "ვაქვეყნებთ მხოლოდ იმ შეფასებებს, რომლებსაც კლიენტი თავად გვიზიარებს და ადმინისტრატორი ამოწმებს.",
            en: "We publish only feedback shared by clients and reviewed by our team.",
            ru: "Мы публикуем только отзывы, которыми поделились клиенты и которые проверены нашей командой.",
        },
    );
    const googleReviewsLabel = translateText(
        translations,
        "home.testimonials.googleReviews",
        locale,
        {
            ka: "Google-ზე შეფასებების ნახვა",
            en: "See reviews on Google",
            ru: "Смотреть отзывы в Google",
        },
    );

    return (
        <section
            aria-labelledby="home-testimonials-title"
            className="border-y border-outline-variant/10 bg-surface-container-low/40"
        >
            <div className="mx-auto max-w-container-max px-5 py-16 sm:px-6 md:px-8 md:py-20 lg:px-14 lg:py-24">
                <header className="mx-auto max-w-3xl text-center">
                    <p className="font-mono-sm text-mono-sm font-semibold uppercase tracking-wider text-primary">
                        {eyebrow}
                    </p>
                    <Typography
                        as="h2"
                        className="mt-3"
                        id="home-testimonials-title"
                        variant="section-title"
                    >
                        {title}
                    </Typography>
                    <p className="mx-auto mt-4 max-w-2xl text-base leading-[1.7] text-on-surface-variant md:text-[17px]">
                        {description}
                    </p>
                </header>

                <div className="mt-10 grid gap-5 md:mt-12 md:grid-cols-2 xl:grid-cols-3">
                    {testimonials.map((testimonial) => {
                        const identity = [testimonial.role, testimonial.company]
                            .filter(Boolean)
                            .join(" · ");

                        return (
                            <figure
                                className="flex h-full flex-col rounded-2xl border border-outline-variant/20 bg-surface-container p-6 shadow-lg shadow-black/5"
                                key={testimonial.id}
                            >
                                <blockquote className="flex-1 text-[17px] leading-relaxed text-on-surface sm:text-lg">
                                    “{testimonial.quote}”
                                </blockquote>
                                <figcaption className="mt-7 flex items-center gap-3 border-t border-outline-variant/15 pt-5">
                                    {testimonial.image ? (
                                        <Image
                                            alt={testimonial.author}
                                            className="size-11 shrink-0"
                                            height={44}
                                            src={resolveBackendAsset(testimonial.image, "")}
                                            variant="avatar"
                                            width={44}
                                        />
                                    ) : (
                                        <span
                                            aria-hidden="true"
                                            className="grid size-11 shrink-0 place-items-center rounded-full bg-primary/15 font-semibold text-primary"
                                        >
                                            {testimonial.author.slice(0, 1).toUpperCase()}
                                        </span>
                                    )}
                                    <span>
                                        <cite className="not-italic font-semibold text-on-surface">
                                            {testimonial.author}
                                        </cite>
                                        {identity ? (
                                            <span className="mt-1 block text-sm text-on-surface-variant">
                                                {identity}
                                            </span>
                                        ) : null}
                                    </span>
                                </figcaption>
                            </figure>
                        );
                    })}
                </div>

                {integrations.googleReviewUrl ? (
                    <div className="mt-8 text-center">
                        <a
                            className="inline-flex min-h-11 items-center justify-center rounded-xl border border-outline-variant/40 px-5 py-2.5 font-medium text-on-surface transition hover:border-primary/60 hover:bg-primary/10"
                            href={integrations.googleReviewUrl}
                            rel="noopener noreferrer"
                            target="_blank"
                        >
                            {googleReviewsLabel}
                        </a>
                    </div>
                ) : null}
            </div>
        </section>
    );
}
